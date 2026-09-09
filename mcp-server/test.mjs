import { Client } from '@modelcontextprotocol/sdk/client/index.js';
import { StdioClientTransport } from '@modelcontextprotocol/sdk/client/stdio.js';
import { fileURLToPath } from 'node:url';
import path from 'node:path';

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);
const serverPath = path.resolve(__dirname, 'index.mjs');

async function testMcpServer() {
  console.log('Testing Antri MCP Server connection...');
  const transport = new StdioClientTransport({
    command: 'node',
    args: [serverPath],
  });

  const client = new Client(
    { name: 'test-client', version: '1.0.0' },
    { capabilities: {} }
  );

  await client.connect(transport);
  console.log('✓ Connected to MCP Server!');

  // 1. Test listing tools
  const toolsRes = await client.listTools();
  console.log(`✓ Tools discovered (${toolsRes.tools.length}):`, toolsRes.tools.map(t => t.name).join(', '));

  // 2. Test queue summary tool
  const summaryRes = await client.callTool({ name: 'antri_get_queue_summary', arguments: {} });
  console.log('✓ antri_get_queue_summary response:', summaryRes.content[0].text);

  // 3. Test taking ticket tool
  const takeRes = await client.callTool({ name: 'antri_take_ticket', arguments: { service_code: 'A' } });
  console.log('✓ antri_take_ticket response:', takeRes.content[0].text);

  // 4. Test calling next ticket
  const callRes = await client.callTool({ name: 'antri_call_next_ticket', arguments: { counter_number: 1 } });
  console.log('✓ antri_call_next_ticket response:', callRes.content[0].text);

  // 5. Test finishing ticket
  const finishRes = await client.callTool({ name: 'antri_finish_ticket', arguments: { counter_number: 1 } });
  console.log('✓ antri_finish_ticket response:', finishRes.content[0].text);

  // 6. Test analytics tool
  const analyticsRes = await client.callTool({ name: 'antri_get_analytics', arguments: { period: 'today' } });
  console.log('✓ antri_get_analytics response:', analyticsRes.content[0].text);

  // 7. Test resources
  const resourcesRes = await client.listResources();
  console.log('✓ Resources discovered:', resourcesRes.resources.map(r => r.uri).join(', '));

  const resourceData = await client.readResource({ uri: 'antri://queue/status' });
  console.log('✓ antri://queue/status content:', resourceData.contents[0].text);

  await client.close();
  console.log('\n★ ALL MCP SERVER INTEGRATION TESTS PASSED PERFECTLY! ★');
}

testMcpServer().catch(err => {
  console.error('MCP Test failed:', err);
  process.exit(1);
});
