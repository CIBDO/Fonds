<style>
    @page {
        size: A4 {{ $orientation ?? 'landscape' }};
        margin: 10mm;
    }

    body {
        font-family: DejaVu Sans, Arial, sans-serif;
        margin: 0;
        padding: 10px;
        font-size: 10px;
        line-height: 1.2;
        color: #000;
    }

    .header { margin-bottom: 20px; }
    .header-left { float: left; width: 45%; text-align: left; }
    .header-right { float: right; width: 45%; text-align: right; }
    .clearfix::after { content: ""; display: table; clear: both; }

    .title { font-weight: bold; font-size: 12px; margin: 3px 0; }
    .subtitle { font-size: 10px; margin: 2px 0; }
    .stars { font-size: 8px; margin: 3px 0; }

    .main-title {
        font-size: 13px;
        font-weight: bold;
        text-decoration: underline;
        margin: 20px 0 8px 0;
        text-align: center;
        text-transform: uppercase;
    }

    .region {
        font-size: 11px;
        font-weight: bold;
        text-align: center;
        margin-bottom: 8px;
    }

    .meta {
        font-size: 10px;
        text-align: center;
        margin-bottom: 12px;
    }

    .note-montants {
        font-size: 9px;
        font-style: italic;
        text-align: center;
        margin: 6px 0 12px;
        color: #666;
    }

    .info-box {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 14px;
        font-size: 10px;
    }

    .info-box td {
        border: 1px solid #000;
        padding: 5px 8px;
        vertical-align: top;
    }

    .info-box .label {
        width: 22%;
        background: #f0f0f0;
        font-weight: bold;
    }

    table.data {
        width: 100%;
        border-collapse: collapse;
        margin: 10px 0;
        font-size: 9px;
    }

    table.data th,
    table.data td {
        border: 1px solid #000;
        padding: 5px 6px;
        text-align: center;
        vertical-align: middle;
    }

    table.data th {
        background-color: #f0f0f0;
        font-weight: bold;
        font-size: 8px;
    }

    .text-left { text-align: left; }
    .text-right { text-align: right; }

    .total-row {
        background-color: #f5f5f5;
        font-weight: bold;
    }

    .section-title {
        font-size: 11px;
        font-weight: bold;
        margin: 16px 0 6px;
        text-decoration: underline;
    }

    .signature {
        margin-right: 80px;
        text-align: right;
        margin-top: 28px;
    }

    .date-signature {
        font-size: 10px;
        margin-bottom: 45px;
    }

    .agent-signature {
        font-size: 10px;
        font-weight: bold;
    }
</style>
