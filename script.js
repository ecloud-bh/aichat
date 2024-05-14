$(document).ready(function() {
    var accessToken, chatId, aiName;

    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        var email = $('#email').val();
        var password = $('#password').val();
        $.post('api.php', { action: 'login', email: email, password: password }, function(response) {
            var result = JSON.parse(response);
            if (result && result.access_token) {
                alert('Login successful');
                accessToken = result.access_token;
                $('#loginForm').hide();
                $('#aiSelectionSection').show();
                loadAiTeam();
            } else {
                alert('Login failed');
            }
        });
    });

    function loadAiTeam() {
        $.post('api.php', { action: 'ai_team_list', access_token: accessToken }, function(response) {
            var team = JSON.parse(response);
            if (team && !team.error) {
                $('#aiList').empty();
                team.forEach(function(member) {
                    $('#aiList').append('<li class="list-group-item" data-ai-id="' + member.ai_id + '">' + member.ai_name + '</li>');
                });
                $('#aiList').on('click', 'li', function() {
                    var aiId = $(this).data('ai-id');
                    startChat(aiId);
                });
            } else {
                alert('Failed to load AI team');
            }
        });
    }

    function startChat(aiId) {
        $.post('api.php', { action: 'newChat', access_token: accessToken, ai_id: aiId }, function(response) {
            var chatData = JSON.parse(response);
            if (chatData && chatData.chat_id) {
                chatId = chatData.chat_id;
                aiName = chatData.ai_name;
                $('#aiSelectionSection').hide();
                $('#chatSection').show();
                $('#chatBox').append('<div class="message ai-message"><span class="message-sender">' + aiName + ':</span><span class="message-content">Chat started with ' + aiName + '</span></div>');
            } else {
                alert('Failed to start chat');
            }
        });
    }

    $('#messageForm').on('submit', function(e) {
        e.preventDefault();
        var message = $('#messageInput').val();
        $.post('api.php', { action: 'sendMessage', access_token: accessToken, chat_id: chatId, message: message }, function(response) {
            var result = JSON.parse(response);
            if (result && result.message) {
                $('#chatBox').append('<div class="message user-message"><span class="message-sender">You:</span><span class="message-content">' + message + '</span></div>');
                $('#chatBox').append('<div class="message ai-message"><span class="message-sender">' + aiName + ':</span><span class="message-content">' + result.message + '</span></div>');
                $('#messageInput').val('');
                $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
            } else {
                alert('Message sending failed: ' + (result.error || 'Unknown error'));
            }
        });
    });

    $('#resetChat').on('click', function() {
        $.post('api.php', { action: 'resetChat', access_token: accessToken, chat_id: chatId }, function(response) {
            var result = JSON.parse(response);
            if (result && !result.error) {
                $('#chatBox').append('<div class="message system-message"><span class="message-sender">System:</span><span class="message-content">Chat has been reset.</span></div>');
                $('#chatBox').scrollTop($('#chatBox')[0].scrollHeight);
            } else {
                alert('Failed to reset chat: ' + (result.error || 'Unknown error'));
            }
        });
    });

    $('#newChat').on('click', function() {
        $('#aiSelectionSection').show();
        $('#chatSection').hide();
        $('#chatBox').empty();
    });
});