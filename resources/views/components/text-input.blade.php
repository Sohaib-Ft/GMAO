@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'bg-white border-gray-300 text-gray-900 rounded-md shadow-sm w-full px-4 py-2 focus:outline-none focus:border-gray-300']) }}>
