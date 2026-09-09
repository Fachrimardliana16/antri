import { Server } from '@modelcontextprotocol/sdk/server/index.js';
import { StdioServerTransport } from '@modelcontextprotocol/sdk/server/stdio.js';
import {
  CallToolRequestSchema,
  ListToolsRequestSchema,
  ListResourcesRequestSchema,
  ReadResourceRequestSchema,
} from '@modelcontextprotocol/sdk/types.js';
import { DatabaseSync } from 'node:sqlite';
import { fileURLToPath } from 'node:url';
import path from 'node:path';
import crypto from 'node:crypto';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const dbPath = path.resolve(__dirname, '../database/database.sqlite');

function getDb() {
  return new DatabaseSync(dbPath);
}

const server = new Server(
  {
    name: 'antri-queue-server',
    version: '1.0.0',
  },
  {
    capabilities: {
      tools: {},
      resources: {},
    },
  }
);

// 1. List Available Tools
server.setRequestHandler(ListToolsRequestSchema, async () => {
  return {
    tools: [
      {
        name: 'antri_get_queue_summary',
        description: 'Get live summary of active counters, waiting queues, and serving tickets per service.',
        inputSchema: {
          type: 'object',
          properties: {},
        },
      },
      {
        name: 'antri_take_ticket',
        description: 'Issue a new queue ticket for a specific service.',
        inputSchema: {
          type: 'object',
          properties: {
            service_code: {
              type: 'string',
              description: 'Service code (e.g. A, B, C, D)',
            },
            service_id: {
              type: 'integer',
              description: 'Service ID (optional if service_code is provided)',
            },
          },
        },
      },
      {
        name: 'antri_call_next_ticket',
        description: 'Call the next waiting ticket for a specific counter.',
        inputSchema: {
          type: 'object',
          properties: {
            counter_number: {
              type: 'integer',
              description: 'Counter number (e.g. 1, 2, 3, 4)',
            },
          },
          required: ['counter_number'],
        },
      },
      {
        name: 'antri_recall_ticket',
        description: 'Re-call the currently active ticket at a counter.',
        inputSchema: {
          type: 'object',
          properties: {
            counter_number: {
              type: 'integer',
              description: 'Counter number',
            },
          },
          required: ['counter_number'],
        },
      },
      {
        name: 'antri_finish_ticket',
        description: 'Complete and finish the ticket currently being served at a counter.',
        inputSchema: {
          type: 'object',
          properties: {
            counter_number: {
              type: 'integer',
              description: 'Counter number',
            },
          },
          required: ['counter_number'],
        },
      },
      {
        name: 'antri_skip_ticket',
        description: 'Skip the ticket currently called at a counter.',
        inputSchema: {
          type: 'object',
          properties: {
            counter_number: {
              type: 'integer',
              description: 'Counter number',
            },
          },
          required: ['counter_number'],
        },
      },
      {
        name: 'antri_update_counter_status',
        description: 'Update the operational status of a counter (active, break, closed).',
        inputSchema: {
          type: 'object',
          properties: {
            counter_number: {
              type: 'integer',
              description: 'Counter number',
            },
            status: {
              type: 'string',
              enum: ['active', 'break', 'closed'],
              description: 'New status for the counter',
            },
          },
          required: ['counter_number', 'status'],
        },
      },
      {
        name: 'antri_get_analytics',
        description: 'Get queue performance metrics, Average Wait Time (AWT), and Average Service Time (AST).',
        inputSchema: {
          type: 'object',
          properties: {
            period: {
              type: 'string',
              enum: ['today', 'week', 'month', 'all'],
              description: 'Time period for analytics calculation (default: today)',
            },
          },
        },
      },
      {
        name: 'antri_update_theme',
        description: 'Update theme colors and branding configurations.',
        inputSchema: {
          type: 'object',
          properties: {
            primary_color: {
              type: 'string',
              description: 'Hex primary color code (e.g. #2563eb)',
            },
            secondary_color: {
              type: 'string',
              description: 'Hex secondary color code (e.g. #06b6d4)',
            },
            app_name: {
              type: 'string',
              description: 'Application branding title',
            },
          },
        },
      },
      {
        name: 'antri_reset_daily',
        description: 'Trigger daily queue reset, finalizing remaining previous queues.',
        inputSchema: {
          type: 'object',
          properties: {},
        },
      },
    ],
  };
});

// 2. Tool Execution Handler
server.setRequestHandler(CallToolRequestSchema, async (request) => {
  const { name, arguments: args } = request.params;
  const db = getDb();
  const todayStr = new Date().toISOString().split('T')[0];

  try {
    switch (name) {
      case 'antri_get_queue_summary': {
        const services = db.prepare('SELECT * FROM services WHERE is_active = 1').all();
        const counters = db.prepare(`
          SELECT c.*, s.name as service_name, s.code as service_code, 
                 t.ticket_number as current_ticket_number, u.name as operator_name
          FROM counters c
          LEFT JOIN services s ON c.service_id = s.id
          LEFT JOIN queue_tickets t ON c.current_ticket_id = t.id
          LEFT JOIN users u ON c.current_operator_id = u.id
          ORDER BY c.number ASC
        `).all();

        const serviceSummaries = services.map((s) => {
          const waiting = db.prepare("SELECT count(*) as count FROM queue_tickets WHERE service_id = ? AND status = 'waiting' AND queue_date = ?")
            .get(s.id, todayStr);

          const served = db.prepare("SELECT count(*) as count FROM queue_tickets WHERE service_id = ? AND status = 'completed' AND queue_date = ?")
            .get(s.id, todayStr);

          return {
            id: s.id,
            code: s.code,
            name: s.name,
            waiting_count: waiting ? waiting.count : 0,
            completed_count: served ? served.count : 0,
            estimated_time_minutes: s.estimated_time_minutes,
          };
        });

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                date: todayStr,
                services: serviceSummaries,
                counters: counters.map((c) => ({
                  number: c.number,
                  name: c.name,
                  status: c.status,
                  service: c.service_name,
                  current_ticket: c.current_ticket_number,
                  operator: c.operator_name,
                })),
              }, null, 2),
            },
          ],
        };
      }

      case 'antri_take_ticket': {
        let service = null;
        if (args?.service_code) {
          service = db.prepare('SELECT * FROM services WHERE code = ? AND is_active = 1').get(args.service_code.toUpperCase());
        } else if (args?.service_id) {
          service = db.prepare('SELECT * FROM services WHERE id = ? AND is_active = 1').get(args.service_id);
        } else {
          service = db.prepare('SELECT * FROM services WHERE is_active = 1 ORDER BY id ASC LIMIT 1').get();
        }

        if (!service) {
          return {
            content: [{ type: 'text', text: 'Error: Service not found or inactive.' }],
            isError: true,
          };
        }

        const lastSeqRow = db.prepare('SELECT MAX(sequence_number) as max_seq FROM queue_tickets WHERE service_id = ? AND queue_date = ?').get(service.id, todayStr);
        const nextSeq = (lastSeqRow?.max_seq || 0) + 1;
        const prefix = service.prefix || service.code;
        const ticketNumber = `${prefix.toUpperCase()}-${String(nextSeq).padStart(3, '0')}`;
        const trackingToken = crypto.randomBytes(16).toString('hex');
        const nowIso = new Date().toISOString();

        const insertStmt = db.prepare(`
          INSERT INTO queue_tickets (ticket_number, sequence_number, service_id, status, tracking_token, queue_date, created_at, updated_at)
          VALUES (?, ?, ?, 'waiting', ?, ?, ?, ?)
        `);
        const result = insertStmt.run(ticketNumber, nextSeq, service.id, trackingToken, todayStr, nowIso, nowIso);
        const ticketId = result.lastInsertRowid;

        db.prepare("INSERT INTO queue_logs (ticket_id, service_id, action, logged_at, created_at, updated_at) VALUES (?, ?, 'created', ?, ?, ?)")
          .run(ticketId, service.id, nowIso, nowIso, nowIso);

        const aheadCountRow = db.prepare("SELECT COUNT(*) as ahead FROM queue_tickets WHERE service_id = ? AND queue_date = ? AND status = 'waiting' AND sequence_number < ?")
          .get(service.id, todayStr, nextSeq);

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                success: true,
                ticket_id: ticketId,
                ticket_number: ticketNumber,
                service_name: service.name,
                sequence_number: nextSeq,
                tickets_ahead: aheadCountRow?.ahead || 0,
                estimated_wait_minutes: ((aheadCountRow?.ahead || 0) + 1) * service.estimated_time_minutes,
                tracking_url: `http://localhost:8000/tracking/${trackingToken}`,
              }, null, 2),
            },
          ],
        };
      }

      case 'antri_call_next_ticket': {
        const counterNumber = Number(args?.counter_number);
        const counter = db.prepare('SELECT * FROM counters WHERE number = ?').get(counterNumber);
        if (!counter) {
          return { content: [{ type: 'text', text: `Error: Counter #${counterNumber} not found.` }], isError: true };
        }
        if (counter.status !== 'active') {
          return { content: [{ type: 'text', text: `Error: Counter #${counterNumber} is currently '${counter.status}', not 'active'.` }], isError: true };
        }

        const nowIso = new Date().toISOString();

        // If there was an existing calling ticket, complete it
        if (counter.current_ticket_id) {
          const oldTicket = db.prepare('SELECT * FROM queue_tickets WHERE id = ?').get(counter.current_ticket_id);
          if (oldTicket && (oldTicket.status === 'calling' || oldTicket.status === 'serving')) {
            const startTime = new Date(oldTicket.served_at || oldTicket.called_at || nowIso).getTime();
            const serviceSecs = Math.max(0, Math.floor((new Date().getTime() - startTime) / 1000));
            db.prepare("UPDATE queue_tickets SET status = 'completed', completed_at = ?, updated_at = ? WHERE id = ?")
              .run(nowIso, nowIso, oldTicket.id);
            db.prepare("INSERT INTO queue_logs (ticket_id, service_id, counter_id, action, service_duration_seconds, logged_at, created_at, updated_at) VALUES (?, ?, ?, 'completed', ?, ?, ?, ?)")
              .run(oldTicket.id, oldTicket.service_id, counter.id, serviceSecs, nowIso, nowIso, nowIso);
          }
        }

        // Find next waiting ticket
        let nextTicket = null;
        if (counter.service_id) {
          nextTicket = db.prepare("SELECT * FROM queue_tickets WHERE service_id = ? AND status = 'waiting' AND queue_date = ? ORDER BY sequence_number ASC LIMIT 1")
            .get(counter.service_id, todayStr);
        } else {
          nextTicket = db.prepare("SELECT * FROM queue_tickets WHERE status = 'waiting' AND queue_date = ? ORDER BY sequence_number ASC LIMIT 1")
            .get(todayStr);
        }

        if (!nextTicket) {
          return { content: [{ type: 'text', text: JSON.stringify({ success: false, message: 'No waiting tickets in queue.' }) }] };
        }

        const createdTime = new Date(nextTicket.created_at).getTime();
        const waitSecs = Math.max(0, Math.floor((new Date().getTime() - createdTime) / 1000));

        db.prepare("UPDATE queue_tickets SET status = 'calling', counter_id = ?, called_at = ?, served_at = ?, updated_at = ? WHERE id = ?")
          .run(counter.id, nowIso, nowIso, nowIso, nextTicket.id);

        db.prepare('UPDATE counters SET current_ticket_id = ? WHERE id = ?')
          .run(nextTicket.id, counter.id);

        db.prepare("INSERT INTO queue_logs (ticket_id, service_id, counter_id, action, wait_duration_seconds, logged_at, created_at, updated_at) VALUES (?, ?, ?, 'called', ?, ?, ?, ?)")
          .run(nextTicket.id, nextTicket.service_id, counter.id, waitSecs, nowIso, nowIso, nowIso);

        const service = db.prepare('SELECT * FROM services WHERE id = ?').get(nextTicket.service_id);

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                success: true,
                action: 'called',
                ticket_number: nextTicket.ticket_number,
                counter_number: counter.number,
                counter_name: counter.name,
                service_name: service?.name,
                voice_text: `Nomor antrean ${nextTicket.ticket_number}, menuju loket ${counter.number}`,
                called_at: nowIso,
              }, null, 2),
            },
          ],
        };
      }

      case 'antri_recall_ticket': {
        const counterNumber = Number(args?.counter_number);
        const counter = db.prepare('SELECT * FROM counters WHERE number = ?').get(counterNumber);
        if (!counter || !counter.current_ticket_id) {
          return { content: [{ type: 'text', text: 'Error: No ticket currently active at this counter.' }], isError: true };
        }

        const ticket = db.prepare('SELECT * FROM queue_tickets WHERE id = ?').get(counter.current_ticket_id);
        const nowIso = new Date().toISOString();

        db.prepare("INSERT INTO queue_logs (ticket_id, service_id, counter_id, action, logged_at, created_at, updated_at) VALUES (?, ?, ?, 'recalled', ?, ?, ?)")
          .run(ticket.id, ticket.service_id, counter.id, nowIso, nowIso, nowIso);

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                success: true,
                action: 'recalled',
                ticket_number: ticket.ticket_number,
                counter_number: counter.number,
                voice_text: `Nomor antrean ${ticket.ticket_number}, menuju loket ${counter.number}`,
              }, null, 2),
            },
          ],
        };
      }

      case 'antri_finish_ticket': {
        const counterNumber = Number(args?.counter_number);
        const counter = db.prepare('SELECT * FROM counters WHERE number = ?').get(counterNumber);
        if (!counter || !counter.current_ticket_id) {
          return { content: [{ type: 'text', text: 'Error: No active ticket to finish at this counter.' }], isError: true };
        }

        const ticket = db.prepare('SELECT * FROM queue_tickets WHERE id = ?').get(counter.current_ticket_id);
        const nowIso = new Date().toISOString();
        const startTime = new Date(ticket.served_at || ticket.called_at || nowIso).getTime();
        const serviceSecs = Math.max(0, Math.floor((new Date().getTime() - startTime) / 1000));

        db.prepare("UPDATE queue_tickets SET status = 'completed', completed_at = ?, updated_at = ? WHERE id = ?")
          .run(nowIso, nowIso, ticket.id);
        db.prepare('UPDATE counters SET current_ticket_id = NULL WHERE id = ?')
          .run(counter.id);
        db.prepare("INSERT INTO queue_logs (ticket_id, service_id, counter_id, action, service_duration_seconds, logged_at, created_at, updated_at) VALUES (?, ?, ?, 'completed', ?, ?, ?, ?)")
          .run(ticket.id, ticket.service_id, counter.id, serviceSecs, nowIso, nowIso, nowIso);

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                success: true,
                message: `Ticket ${ticket.ticket_number} finished successfully.`,
                service_duration_seconds: serviceSecs,
              }, null, 2),
            },
          ],
        };
      }

      case 'antri_skip_ticket': {
        const counterNumber = Number(args?.counter_number);
        const counter = db.prepare('SELECT * FROM counters WHERE number = ?').get(counterNumber);
        if (!counter || !counter.current_ticket_id) {
          return { content: [{ type: 'text', text: 'Error: No active ticket to skip at this counter.' }], isError: true };
        }

        const ticket = db.prepare('SELECT * FROM queue_tickets WHERE id = ?').get(counter.current_ticket_id);
        const nowIso = new Date().toISOString();

        db.prepare("UPDATE queue_tickets SET status = 'skipped', completed_at = ?, updated_at = ? WHERE id = ?")
          .run(nowIso, nowIso, ticket.id);
        db.prepare('UPDATE counters SET current_ticket_id = NULL WHERE id = ?')
          .run(counter.id);
        db.prepare("INSERT INTO queue_logs (ticket_id, service_id, counter_id, action, logged_at, created_at, updated_at) VALUES (?, ?, ?, 'skipped', ?, ?, ?)")
          .run(ticket.id, ticket.service_id, counter.id, nowIso, nowIso, nowIso);

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                success: true,
                message: `Ticket ${ticket.ticket_number} skipped.`,
              }, null, 2),
            },
          ],
        };
      }

      case 'antri_update_counter_status': {
        const counterNumber = Number(args?.counter_number);
        const status = args?.status;
        const counter = db.prepare('SELECT * FROM counters WHERE number = ?').get(counterNumber);
        if (!counter) {
          return { content: [{ type: 'text', text: `Error: Counter #${counterNumber} not found.` }], isError: true };
        }

        db.prepare('UPDATE counters SET status = ?, updated_at = ? WHERE id = ?')
          .run(status, new Date().toISOString(), counter.id);

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                success: true,
                counter_number: counterNumber,
                new_status: status,
              }, null, 2),
            },
          ],
        };
      }

      case 'antri_get_analytics': {
        const period = args?.period || 'today';
        let dateFilter = todayStr;
        if (period === 'week') {
          const d = new Date();
          d.setDate(d.getDate() - 7);
          dateFilter = d.toISOString().split('T')[0];
        } else if (period === 'month') {
          const d = new Date();
          d.setDate(d.getDate() - 30);
          dateFilter = d.toISOString().split('T')[0];
        } else if (period === 'all') {
          dateFilter = '1970-01-01';
        }

        const totalRow = db.prepare('SELECT COUNT(*) as count FROM queue_tickets WHERE queue_date >= ?').get(dateFilter);
        const completedRow = db.prepare("SELECT COUNT(*) as count FROM queue_tickets WHERE queue_date >= ? AND status = 'completed'").get(dateFilter);
        const skippedRow = db.prepare("SELECT COUNT(*) as count FROM queue_tickets WHERE queue_date >= ? AND status = 'skipped'").get(dateFilter);
        const waitingRow = db.prepare("SELECT COUNT(*) as count FROM queue_tickets WHERE queue_date >= ? AND status = 'waiting'").get(dateFilter);

        const avgWaitRow = db.prepare('SELECT AVG(wait_duration_seconds) as avg_wait FROM queue_logs WHERE logged_at >= ? AND wait_duration_seconds > 0').get(dateFilter);
        const avgServiceRow = db.prepare('SELECT AVG(service_duration_seconds) as avg_svc FROM queue_logs WHERE logged_at >= ? AND service_duration_seconds > 0').get(dateFilter);

        const avgWaitSecs = Math.round(avgWaitRow?.avg_wait || 0);
        const avgSvcSecs = Math.round(avgServiceRow?.avg_svc || 0);

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                period,
                total_tickets: totalRow?.count || 0,
                completed_tickets: completedRow?.count || 0,
                skipped_tickets: skippedRow?.count || 0,
                waiting_tickets: waitingRow?.count || 0,
                average_wait_time_seconds: avgWaitSecs,
                average_wait_time_formatted: `${Math.floor(avgWaitSecs / 60)}m ${avgWaitSecs % 60}s`,
                average_service_time_seconds: avgSvcSecs,
                average_service_time_formatted: `${Math.floor(avgSvcSecs / 60)}m ${avgSvcSecs % 60}s`,
              }, null, 2),
            },
          ],
        };
      }

      case 'antri_update_theme': {
        const nowIso = new Date().toISOString();
        if (args?.primary_color) {
          db.prepare('INSERT INTO app_settings (key, value, created_at, updated_at) VALUES ("primary_color", ?, ?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = excluded.updated_at')
            .run(args.primary_color, nowIso, nowIso);
        }
        if (args?.secondary_color) {
          db.prepare('INSERT INTO app_settings (key, value, created_at, updated_at) VALUES ("secondary_color", ?, ?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = excluded.updated_at')
            .run(args.secondary_color, nowIso, nowIso);
        }
        if (args?.app_name) {
          db.prepare('INSERT INTO app_settings (key, value, created_at, updated_at) VALUES ("app_name", ?, ?, ?) ON CONFLICT(key) DO UPDATE SET value = excluded.value, updated_at = excluded.updated_at')
            .run(args.app_name, nowIso, nowIso);
        }

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                success: true,
                message: 'Theme settings updated successfully.',
                applied: args,
              }, null, 2),
            },
          ],
        };
      }

      case 'antri_reset_daily': {
        const nowIso = new Date().toISOString();
        db.prepare("UPDATE queue_tickets SET status = 'completed', completed_at = ?, updated_at = ? WHERE status IN ('waiting', 'calling', 'serving') AND queue_date < ?")
          .run(nowIso, nowIso, todayStr);
        db.prepare('UPDATE counters SET current_ticket_id = NULL, updated_at = ?')
          .run(nowIso);

        return {
          content: [
            {
              type: 'text',
              text: JSON.stringify({
                success: true,
                message: 'Daily queue reset executed.',
                reset_at: nowIso,
              }, null, 2),
            },
          ],
        };
      }

      default:
        return {
          content: [{ type: 'text', text: `Unknown tool: ${name}` }],
          isError: true,
        };
    }
  } catch (err) {
    return {
      content: [{ type: 'text', text: `Internal Error: ${err.message}` }],
      isError: true,
    };
  }
});

// 3. Resources Handlers
server.setRequestHandler(ListResourcesRequestSchema, async () => {
  return {
    resources: [
      {
        uri: 'antri://queue/status',
        name: 'Live Queue Status',
        mimeType: 'application/json',
      },
      {
        uri: 'antri://settings/theme',
        name: 'System Theme Settings',
        mimeType: 'application/json',
      },
    ],
  };
});

server.setRequestHandler(ReadResourceRequestSchema, async (request) => {
  const uri = request.params.uri;
  const db = getDb();
  const todayStr = new Date().toISOString().split('T')[0];

  if (uri === 'antri://queue/status') {
    const services = db.prepare('SELECT id, name, code, estimated_time_minutes FROM services WHERE is_active = 1').all();
    const counters = db.prepare('SELECT number, name, status, service_id, current_ticket_id FROM counters').all();
    return {
      contents: [
        {
          uri,
          mimeType: 'application/json',
          text: JSON.stringify({ date: todayStr, services, counters }, null, 2),
        },
      ],
    };
  }

  if (uri === 'antri://settings/theme') {
    const settings = db.prepare('SELECT key, value FROM app_settings').all();
    const map = {};
    settings.forEach((s) => { map[s.key] = s.value; });
    return {
      contents: [
        {
          uri,
          mimeType: 'application/json',
          text: JSON.stringify(map, null, 2),
        },
      ],
    };
  }

  throw new Error(`Resource not found: ${uri}`);
});

// Start the stdio transport
const transport = new StdioServerTransport();
await server.connect(transport);
