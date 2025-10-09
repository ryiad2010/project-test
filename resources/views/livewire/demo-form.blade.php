<div class="max-w-5xl mx-auto w-full p-8 space-y-6">
    <form wire:submit.prevent="submit">
        {{ $this->form }}
        <div class="mt-6">
            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Submit
            </button>
        </div>
    </form>

    <div class="mt-8 p-4 bg-gray-100 rounded">
        <h3 class="font-bold mb-2">Form Data:</h3>
        <pre class="text-sm">{{ json_encode($this->data, JSON_PRETTY_PRINT) }}</pre>
    </div>
</div>
