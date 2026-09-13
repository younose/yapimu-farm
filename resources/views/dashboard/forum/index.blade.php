@extends('layouts.dashboard')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card forum-card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Forum {{ config('app.name') }}</h4>
                    <small class="text-muted">Satu forum besar untuk semua investor</small>
                </div>
                <div class="card-body p-0">
                    <div id="forum-messages" class="forum-messages" data-last-id="{{ optional($messages->last())->id ?? 0 }}">
                        @forelse ($messages as $message)
                            <div class="forum-bubble-row {{ $message->user_id === auth()->id() ? 'is-me' : '' }}"
                                data-message-id="{{ $message->id }}">
                                @if ($message->user_id !== auth()->id())
                                    <img src="{{ $message->user->photo_url }}" class="forum-avatar" alt=""
                                        onerror="this.onerror=null;this.src='{{ asset('images/avatar/1.png') }}'">
                                @endif
                                <div class="forum-bubble">
                                    @if ($message->user_id !== auth()->id())
                                        <div class="forum-bubble-name">{{ $message->user->name }}</div>
                                    @endif
                                    <div class="forum-bubble-text">{{ $message->message }}</div>
                                    <div class="forum-bubble-time">{{ $message->created_at->format('d M Y H:i') }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-5">Belum ada pesan. Mulai diskusi pertama!</div>
                        @endforelse
                    </div>
                    <form id="forum-form" class="forum-input-bar">
                        @csrf
                        <input type="text" name="message" id="forum-input" class="form-control" placeholder="Tulis pesan..." autocomplete="off" required maxlength="2000">
                        <button type="submit" class="btn btn-primary forum-send-btn">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script>
        (function() {
            const currentUserId = {{ auth()->id() }};
            const messagesEl = document.getElementById('forum-messages');
            const form = document.getElementById('forum-form');
            const input = document.getElementById('forum-input');

            function scrollToBottom() {
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function appendMessage(message) {
                if (messagesEl.querySelector('[data-message-id="' + message.id + '"]')) {
                    return;
                }

                const isMe = message.user.id === currentUserId;
                const row = document.createElement('div');
                row.className = 'forum-bubble-row' + (isMe ? ' is-me' : '');
                row.setAttribute('data-message-id', message.id);

                row.innerHTML =
                    (isMe ? '' : '<img src="' + message.user.photo_url + '" class="forum-avatar" alt="" onerror="this.onerror=null;this.src=\'{{ asset('images/avatar/1.png') }}\'">') +
                    '<div class="forum-bubble">' +
                    (isMe ? '' : '<div class="forum-bubble-name">' + escapeHtml(message.user.name) + '</div>') +
                    '<div class="forum-bubble-text">' + escapeHtml(message.message) + '</div>' +
                    '<div class="forum-bubble-time">' + message.created_at + '</div>' +
                    '</div>';

                messagesEl.appendChild(row);
                messagesEl.setAttribute('data-last-id', message.id);
            }

            scrollToBottom();

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const value = input.value.trim();

                if (!value) {
                    return;
                }

                input.disabled = true;

                fetch('{{ route('forum.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            message: value
                        }),
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success) {
                            appendMessage(res.data);
                            scrollToBottom();
                            input.value = '';
                        }
                    })
                    .finally(() => {
                        input.disabled = false;
                        input.focus();
                    });
            });

            function poll() {
                const lastId = messagesEl.getAttribute('data-last-id') || 0;

                fetch('{{ route('forum.poll') }}?after_id=' + lastId, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(res => {
                        if (res.success && res.data.length) {
                            res.data.forEach(appendMessage);
                            scrollToBottom();
                        }
                    })
                    .catch(() => {})
                    .finally(() => {
                        setTimeout(poll, 4000);
                    });
            }

            setTimeout(poll, 4000);
        })();
    </script>
@endpush
