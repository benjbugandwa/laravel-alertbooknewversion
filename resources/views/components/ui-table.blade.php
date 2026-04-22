@props([
    'headers' => [],
])

<div class="overflow-x-auto bg-white border border-gray-200 rounded-2xl shadow-sm">
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 text-gray-600">
            <tr>
                @foreach ($headers as $h)
                    <th class="text-left font-medium px-4 py-3 border-b">{{ $h }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody class="divide-y">
            {{ $slot }}
        </tbody>
    </table>
</div>
