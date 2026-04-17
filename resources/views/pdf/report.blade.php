<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
            color: #111;
        }

        h1 {
            text-align: center;
            font-size: 26px;
            margin-bottom: 10px;
        }

        .date {
            text-align: center;
            margin-bottom: 20px;
            font-size: 13px;
            color: #555;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            background: #2bb39a;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
            font-size: 13px;
        }

        .right {
            text-align: right;
        }

        .income {
            color: green;
            font-weight: bold;
        }

        .expense {
            color: red;
            font-weight: bold;
        }

        .totals {
            margin-top: 30px;
            font-weight: bold;
            font-size: 14px;
        }

        .totals p {
            margin: 5px 0;
        }

        .net {
            font-size: 16px;
        }

        .empty {
            text-align: center;
            margin-top: 120px;
        }

        .empty h2 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .empty p {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>
<body>

@php
    $hasData = !($totalIncome == 0 && abs($totalExpenses) == 0 && $netIncome == 0);
@endphp

@if(!$hasData)

    {{-- ❌ EMPTY STATE --}}
    <div class="empty">
        <h2>No Report Data Available</h2>
        <p>
            This report cannot be generated because there are no valid transactions
            or the calculated values are zero.
        </p>
    </div>

@else

    {{-- ✅ BUSINESS NAME --}}
    <h1>{{ strtoupper($report->business_name ?? 'BUSINESS ENTITY') }}</h1>

    {{-- ✅ DATE RANGE --}}
    <div class="date">
        {{ $report->date_range ?? 'Jan 1 - Jan 31, 2026' }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th>Type</th>
                <th class="right">Amount</th>
            </tr>
        </thead>
        <tbody>

           @foreach($groupedCategories as $cat => $amount)

    @if($amount != 0)

        @php
            $isIncome = $amount > 0;
        @endphp

        <tr>
            <td>{{ $cat }}</td>

            <td class="{{ $isIncome ? 'income' : 'expense' }}">
                {{ $isIncome ? 'Income' : 'Expense' }}
            </td>

            <td class="right {{ $isIncome ? 'income' : 'expense' }}">
                ${{ number_format(abs($amount), 2) }}
            </td>
        </tr>

    @endif

@endforeach 

        </tbody>
    </table>

    <div class="totals">
        <p>
            TOTAL INCOME:
            <span class="income">
                ${{ number_format($totalIncome, 2) }}
            </span>
        </p>

        <p>
            TOTAL EXPENSES:
            <span class="expense">
                ${{ number_format(abs($totalExpenses), 2) }}
            </span>
        </p>

        <p class="net">
            NET INCOME:
            <span class="{{ $netIncome >= 0 ? 'income' : 'expense' }}">
                ${{ number_format($netIncome, 2) }}
            </span>
        </p>
    </div>

@endif

</body>
</html>