<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportConfig['title'] ?? 'Revenue Report' }} - {{ $companyConfig['name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            color: #1f2937;
            background: #fff;
            padding: 40px;
        }

        /* Header */
        .report-header {
            text-align: center;
            border-bottom: 3px solid #1f2937;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .report-header .logo {
            max-height: 60px;
            margin-bottom: 10px;
        }

        .report-header h1 {
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .report-header .company-name {
            font-size: 16px;
            font-weight: 600;
            color: #374151;
        }

        .report-header .company-details {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Report Meta */
        .report-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding: 15px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
        }

        .report-meta .meta-group {
            font-size: 11px;
        }

        .report-meta .meta-label {
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }

        .report-meta .meta-value {
            color: #1f2937;
            font-weight: 500;
        }

        /* Summary Cards */
        .summary-section {
            margin-bottom: 30px;
        }

        .summary-section h2 {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #374151;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        .summary-grid {
            display: flex;
            gap: 20px;
        }

        .summary-card {
            flex: 1;
            padding: 20px;
            border: 2px solid #1f2937;
            text-align: center;
        }

        .summary-card .label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6b7280;
            font-weight: 600;
        }

        .summary-card .value {
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
            margin-top: 5px;
        }

        .summary-card.highlight {
            background: #1f2937;
            color: #fff;
        }

        .summary-card.highlight .label {
            color: #d1d5db;
        }

        .summary-card.highlight .value {
            color: #fff;
        }

        /* Service Breakdown */
        .breakdown-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .breakdown-item {
            flex: 1;
            min-width: 150px;
            padding: 15px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .breakdown-item .service-name {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: 600;
        }

        .breakdown-item .service-revenue {
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
            margin-top: 3px;
        }

        .breakdown-item .service-count {
            font-size: 10px;
            color: #9ca3af;
            margin-top: 2px;
        }

        /* Table */
        .table-section {
            margin-bottom: 30px;
        }

        .table-section h2 {
            font-size: 14px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #374151;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e5e7eb;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 11px;
        }

        .data-table thead {
            background: #1f2937;
            color: #fff;
        }

        .data-table th {
            padding: 12px 10px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }

        .data-table th:last-child {
            text-align: right;
        }

        .data-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        .data-table td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .data-table tbody tr:nth-child(even) {
            background: #f9fafb;
        }

        .data-table tfoot {
            background: #f3f4f6;
            font-weight: 700;
        }

        .data-table tfoot td {
            padding: 12px 10px;
            border-top: 2px solid #1f2937;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border: 1px solid;
            border-radius: 3px;
        }

        .status-completed {
            background: #d1fae5;
            color: #065f46;
            border-color: #10b981;
        }

        .status-paid {
            background: #dbeafe;
            color: #1e40af;
            border-color: #3b82f6;
        }

        /* Footer */
        .report-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
        }

        .report-footer .footer-text {
            font-size: 10px;
            color: #9ca3af;
            font-style: italic;
        }

        .report-footer .footer-company {
            font-size: 11px;
            color: #6b7280;
            margin-top: 5px;
        }

        /* Signature Section */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
            padding-top: 20px;
        }

        .signature-box {
            width: 200px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #1f2937;
            margin-bottom: 5px;
            padding-top: 40px;
        }

        .signature-label {
            font-size: 10px;
            color: #6b7280;
            text-transform: uppercase;
        }

        /* Print Styles */
        @media print {
            body {
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            @page {
                size: {{ $printConfig['page_size'] ?? 'A4' }} {{ $printConfig['orientation'] ?? 'portrait' }};
                margin: {{ $printConfig['margin_top'] ?? '1.5cm' }} {{ $printConfig['margin_right'] ?? '1.5cm' }} {{ $printConfig['margin_bottom'] ?? '1.5cm' }} {{ $printConfig['margin_left'] ?? '1.5cm' }};
            }

            .no-print {
                display: none !important;
            }

            .data-table {
                page-break-inside: auto;
            }

            .data-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }

        /* Action Buttons */
        .action-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #1f2937;
            padding: 15px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        .action-bar .title {
            color: #fff;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: #3b82f6;
            color: #fff;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-success {
            background: #10b981;
            color: #fff;
        }

        .btn-success:hover {
            background: #059669;
        }

        body.has-action-bar {
            padding-top: 80px;
        }
    </style>
</head>
<body class="has-action-bar">
    <!-- Action Bar (hidden when printing) -->
    <div class="action-bar no-print">
        <span class="title">Revenue Report Preview</span>
        <div class="action-buttons">
            <a href="{{ route('admin.revenue.index', request()->all()) }}" class="btn btn-secondary">
                ← Back
            </a>
            <a href="{{ route('admin.revenue.export.csv', request()->all()) }}" class="btn btn-success">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="7 10 12 15 17 10"/>
                    <line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Export CSV
            </a>
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                Print / Save PDF
            </button>
        </div>
    </div>

    <!-- Report Header -->
    <div class="report-header">
        @if($reportConfig['show_logo'] && $companyConfig['logo'])
            <img src="{{ asset($companyConfig['logo']) }}" alt="Logo" class="logo">
        @endif
        <h1>{{ $reportConfig['title'] ?? 'Revenue Report' }}</h1>
        @if($reportConfig['show_company_info'])
            <div class="company-name">{{ $companyConfig['name'] }}</div>
            @if($companyConfig['address'] || $companyConfig['phone'] || $companyConfig['email'])
                <div class="company-details">
                    @if($companyConfig['address']){{ $companyConfig['address'] }}@endif
                    @if($companyConfig['address'] && ($companyConfig['phone'] || $companyConfig['email'])) • @endif
                    @if($companyConfig['phone'])Tel: {{ $companyConfig['phone'] }}@endif
                    @if($companyConfig['phone'] && $companyConfig['email']) • @endif
                    @if($companyConfig['email']){{ $companyConfig['email'] }}@endif
                </div>
            @endif
        @endif
    </div>

    <!-- Report Meta Information -->
    <div class="report-meta">
        <div class="meta-group">
            <div class="meta-label">Report Period</div>
            <div class="meta-value">{{ $allTime ? 'All Time' : $start->format($dateFormat) . ' — ' . $end->format($dateFormat) }}</div>
        </div>
        @if($reportConfig['show_report_id'])
        <div class="meta-group">
            <div class="meta-label">Report ID</div>
            <div class="meta-value">REV-{{ now()->format('YmdHis') }}</div>
        </div>
        @endif
        <div class="meta-group">
            <div class="meta-label">Generated</div>
            <div class="meta-value">{{ now()->format($dateFormat . ' ' . $timeFormat) }}</div>
        </div>
        @if($reportConfig['show_generated_by'])
        <div class="meta-group">
            <div class="meta-label">Prepared By</div>
            <div class="meta-value">{{ Auth::guard('admin')->user()->fname }} {{ Auth::guard('admin')->user()->lname }}</div>
        </div>
        @endif
    </div>

    <!-- Summary Section -->
    <div class="summary-section">
        <h2>Financial Summary</h2>
        <div class="summary-grid">
            <div class="summary-card highlight">
                <div class="label">Total Revenue</div>
                <div class="value">{{ $currency }}{{ number_format($totalRevenue, $reportConfig['decimal_places'] ?? 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Total Transactions</div>
                <div class="value">{{ number_format($totalTransactions) }}</div>
            </div>
            <div class="summary-card">
                <div class="label">Average Order Value</div>
                <div class="value">{{ $currency }}{{ number_format($averageOrder, $reportConfig['decimal_places'] ?? 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Revenue by Service -->
    @if($revenueByService->count() > 0)
    <div class="summary-section">
        <h2>Revenue by Service Type</h2>
        <div class="breakdown-grid">
            @foreach($revenueByService as $service => $data)
                <div class="breakdown-item">
                    <div class="service-name">{{ ucfirst(str_replace('_', ' ', $service)) }}</div>
                    <div class="service-revenue">{{ $currency }}{{ number_format($data['revenue'], $reportConfig['decimal_places'] ?? 2) }}</div>
                    <div class="service-count">{{ $data['count'] }} transaction(s)</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Transaction Details -->
    <div class="table-section">
        <h2>Transaction Details ({{ $transactions->count() }} records)</h2>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>Customer</th>
                    <th>Service Type</th>
                    <th>Status</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at->format('M d, Y') }}</td>
                        <td>#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $transaction->user ? $transaction->user->fname . ' ' . $transaction->user->lname : 'N/A' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $transaction->item_type)) }}</td>
                        <td>
                            <span class="status-badge status-{{ $transaction->status }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td>{{ $currency }}{{ number_format($transaction->total_price, $reportConfig['decimal_places'] ?? 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 30px; color: #9ca3af;">
                            No transactions found for this period
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if($transactions->count() > 0)
            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right;">Total Revenue</td>
                    <td>{{ $currency }}{{ number_format($totalRevenue, $reportConfig['decimal_places'] ?? 2) }}</td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

    <!-- Signature Section (optional) -->
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Prepared By</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Verified By</div>
        </div>
        <div class="signature-box">
            <div class="signature-line"></div>
            <div class="signature-label">Approved By</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="report-footer">
        <div class="footer-text">{{ $reportConfig['footer_text'] ?? 'This is a computer-generated report.' }}</div>
        <div class="footer-company">{{ $companyConfig['name'] }} • Generated on {{ now()->format($dateFormat . ' ' . $timeFormat) }}</div>
    </div>
</body>
</html>
