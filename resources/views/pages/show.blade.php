@foreach ($page->content as $block)
    @php
        $data = $block['data'] ?? [];
    @endphp

    @switch($block['type'])
        @case('heading')
            <h{{ $data['level'] ?? 'h1' }}>
                {{ $data['content'] ?? '' }}
                </h{{ $data['level'] ?? 'h1' }}>
            @break

            @case('paragraph')
                <p>{!! nl2br(e($data['content'] ?? '')) !!}</p>
            @break

            @case('image')
                <img src="{{ asset('storage/' . ($data['url'] ?? '')) }}" alt="{{ $data['alt'] ?? '' }}">
            @break
        @endswitch
@endforeach
