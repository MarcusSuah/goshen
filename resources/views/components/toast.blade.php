@props(['on', 'default' => ''])

<div x-data="{
    showToast: false,
    message: '{{ $default ?: $slot }}',
    timeout: null
}" x-init="window.addEventListener('{{ $on }}', event => {
    clearTimeout(timeout);
    message = event.detail?.message || '{{ $default ?: $slot }}';
    showToast = true;
    timeout = setTimeout(() => showToast = false, 3000);
})" x-show="showToast" x-transition
    class="fixed top-5 right-5 bg-green-600 text-white text-sm px-4 py-4 rounded-lg shadow-lg z-50 {{ $attributes->get('class') }}">
    <p x-text="message"></p>
</div>
