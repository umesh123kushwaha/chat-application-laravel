@extends('layouts.app')
@section('mycss')
    <style>
        .chat-messages {
            width: 100%;
            min-height: 400px;
            max-height: 500px;
            overflow: auto;
            display: block;
            background: white
        }

        .message-from {
            padding: 10px;

            margin-bottom: 10px;
            width: 70%;
            float: left;
        }

        .message-from .message-text {
            padding: 10px;
            border-radius: 10px;
            background: #e5e5ea;


        }

        .message-to {
            padding: 10px;

            margin-bottom: 10px;
            width: 70%;
            float: right;

            text-align: right
        }

        .message-to .message-text {
            background: #007bff;
            padding: 10px;
            border-radius: 10px;

            color: white;

        }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                @foreach ($users as $user)
                    <div class="row">
                        <div class="col-md-12 user" data-id="{{ $user->id }}">
                            <h5>{{ $user->name }} @if ($user->unreadMessagesFromCount() > 0)
                                    <span class="badge bg-success">{{ $user->unreadMessagesFromCount() }}</span>
                                @endif
                            </h5>
                            <p class="last-message">{{$user->lastMessage()->message}}</p>

                        </div>
                    </div>
                @endforeach

            </div>
            <div class="col-md-8">
                <input type="hidden" class="message-from-user" value="{{ auth()->user()->id }}">
                <div class="card chat-container">


                </div>
            </div>
        </div>
    @endsection
    @section('customjs')
        <script>
            Echo.private('chat')
                .listen('MessageSent', (e) => {
                    console.log(e);
                    let message_to = $('.message-user-to').val();
                    let message_from = $('.message-from-user').val();

                    if (e.message.from == message_from || e.message.to == message_from) {
                        if (e.message.from == message_to && e.message.to == message_from) {
                            $('.chat-messages').append('<p class="message-from"> <span class="message-text">' + e.message
                                .message +
                                '<span></p>');

                            $(".chat-messages").animate({
                                scrollTop: $('.chat-messages')[0].scrollHeight - $('.chat-messages')[0].clientHeight
                            }, 1000);
                        }
                        if (e.user.id == message_to) {
                            $('.user[data-id=' + e.user.id + ']').html(
                                `<h5>${e.user.name}</h5><p class="last-message">${e.message.message}</p>`);
                            updateMessageStatus(e.message.id);
                        } else {
                            $('.user[data-id=' + e.user.id + ']').html(
                                `<h5>${e.user.name}<span class="badge bg-success">${e.unread_message}</span></h5><p class="last-message">${e.message.message}</p>`
                            );
                        }
                    }

                })
            $(document).ready(function() {
                $(document).on('click', '.send-btn', function() {
                    let message = $('.message-input-text').val().trim();
                    let message_to = $('.message-user-to').val();
                    if (message.length > 0) {
                        sendMessage(message, message_to);
                    }
                })

                $(document).on('click', '.user', function() {
                    user = $(this).attr('data-id');
                    getMessages(user);
                })
            });

            function sendMessage(message, to) {
                $.ajax({
                    url: `{{ route('send-message') }}`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name=csrf-token]').attr('content'),
                        message: message,
                        to: to
                    },
                    success: function(res) {
                        $('.chat-messages').append('<p class="message-to"><span class="message-text">' + message +
                            '</span></p>');
                        $(".chat-messages").animate({
                            scrollTop: $('.chat-messages')[0].scrollHeight - $('.chat-messages')[0]
                                .clientHeight
                        }, 1000);
                        $('.message-input-text').val("");
                    },
                    error: function(e) {
                        onsole.log(e);
                    }

                });
            }

            function getMessages(user) {
                $.ajax({
                    url: `{{ route('get-message') }}`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name=csrf-token]').attr('content'),
                        user: user

                    },
                    success: function(res) {

                        message_html = '';
                        res.messages.forEach(element => {
                            if (element.from == res.user.id) {
                                message_html = message_html +
                                    '<p class="message-from"><span class="message-text">' +
                                    element.message + '</span></p>';
                            } else {
                                message_html = message_html +
                                    '<p class="message-to"><span class="message-text">' +
                                    element.message + '</span></p>';
                            }

                        });
                        $('.chat-container').html(`
                        <div class="card-header user-message-header">${res.user.name}</div>

                        <div class="card-body">

                            <div class="chat-messages">${message_html}

                            </div>

                            <div class="input-group">
                                <input type="hidden" name="message_user_to" class="message-user-to" value="${res.user.id}">
                                <input type="text" class="form-control rounded-0 message-input-text"
                                    id="validationTooltipUsername" placeholder="Type here..."
                                    aria-describedby="validationTooltipUsernamePrepend">
                                <div class="input-group-postpend">
                                    <button class="btn btn-primary rounded-0 send-btn">Send</button>
                                </div>
                            </div>

                        </div>

                        `);


                    },
                    error: function(e) {
                        console.log(e);
                    }

                });


            }

            function updateMessageStatus(message_id) {
                $.ajax({
                    url: `{{ route('update-message-status') }}`,
                    method: 'POST',
                    data: {
                        _token: $('meta[name=csrf-token]').attr('content'),
                        message_id: message_id

                    },
                    success: function(res) {
                        console.log(res);
                    },
                    error: function(e) {
                        console.log(e);
                    }

                });
            }
        </script>
    @endsection
