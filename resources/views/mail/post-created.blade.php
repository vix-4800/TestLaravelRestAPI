<x-mail::message>
    # New Post Created

    Title: {{ $post->title }}

    Body: {{ $post->body }}

    Thanks,
    {{ config('app.name') }}
</x-mail::message>
