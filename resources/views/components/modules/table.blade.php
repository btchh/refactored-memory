@props([
    'headers' => [],
    'striped' => true,
    'hoverable' => true,
])

@php
    $tableClasses = 'table w-full border-collapse';
    $tbodyClasses = $striped ? 'striped' : '';
@endphp

<div class="table-container overflow-x-auto">
    <table {{ $attributes->merge(['class' => $tableClasses]) }}>
        @if(!empty($headers))
            <thead class="bg-gray-50 border-b-2 border-gray-200">
                <tr>
                    @foreach($headers as $header)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                </tr>
            </thead>
        @endif
        
        <tbody class="{{ $tbodyClasses }}">
            {{ $slot }}
        </tbody>
    </table>
</div>

<style>
    .table tbody.striped tr:nth-child(even) {
        background-color: #f9fafb;
    }
    
    .table tbody tr:hover {
        background-color: #f3f4f6;
    }
    
    .table tbody tr {
        border-bottom: 1px solid #e5e7eb;
    }
    
    .table tbody td {
        padding: 1rem 1.5rem;
        text-align: left;
    }
</style>
