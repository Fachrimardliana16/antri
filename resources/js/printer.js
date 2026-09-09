// Silent Printing & Thermal Receipt Engine
class AntriPrinter {
    static printTicket(ticket) {
        // Create hidden iframe for silent printing
        let iframe = document.getElementById('print-frame');
        if (!iframe) {
            iframe = document.createElement('iframe');
            iframe.id = 'print-frame';
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);
        }

        const appName = window.__APP_NAME || 'SISTEM ANTRIAN TERPADU';
        const doc = iframe.contentWindow.document;
        doc.open();
        doc.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Struk Antrean</title>
                <style>
                    @page {
                        margin: 0;
                        size: 58mm auto;
                    }
                    body {
                        font-family: 'Courier New', monospace;
                        width: 58mm;
                        margin: 0;
                        padding: 8px;
                        color: #000;
                        text-align: center;
                    }
                    .header { font-size: 13px; font-weight: bold; margin-bottom: 4px; text-transform: uppercase; }
                    .sub { font-size: 10px; margin-bottom: 6px; }
                    .divider { border-top: 1px dashed #000; margin: 6px 0; }
                    .service { font-size: 11px; font-weight: bold; margin: 4px 0; }
                    .number { font-size: 28px; font-weight: bold; margin: 6px 0; letter-spacing: 2px; }
                    .qr { margin: 6px auto; display: block; }
                    .info { font-size: 9px; line-height: 1.3; }
                    .footer { font-size: 9px; margin-top: 8px; font-style: italic; }
                </style>
            </head>
            <body>
                <div class="header">${appName}</div>
                <div class="sub">${new Date().toLocaleString('id-ID')}</div>
                <div class="divider"></div>
                <div class="service">${ticket.service_name}</div>
                <div class="number">${ticket.ticket_number}</div>
                <div class="divider"></div>
                ${ticket.qr_svg ? `<div class="qr">${ticket.qr_svg}</div>` : ''}
                <div class="info">
                    <div>Sisa antrean di depan: <strong>${ticket.ahead_count ?? 0}</strong></div>
                    <div>Estimasi tunggu: <strong>${ticket.estimated_wait ?? 5} menit</strong></div>
                    <div>Scan QR untuk pantau antrean di HP</div>
                </div>
                <div class="divider"></div>
                <div class="footer">Terima kasih atas kesabaran Anda</div>
            </body>
            </html>
        `);
        doc.close();

        setTimeout(() => {
            try {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            } catch (e) {
                console.warn('Silent print error:', e);
            }
        }, 250);
    }
}

window.AntriPrinter = AntriPrinter;
