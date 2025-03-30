document.addEventListener('DOMContentLoaded', () => {
    const messageArea = document.getElementById('messageArea');
    const messageInput = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const sendBtn = document.getElementById('sendBtn');
    const reminderBox = document.getElementById('reminderBox');
    let hasUploadedLetter = false;

    // Check if employment letter was uploaded
    function checkEmploymentLetter() {
        if (!hasUploadedLetter) {
            messageInput.disabled = true;
            messageInput.placeholder = "Please upload employment letter first";
        }
    }

    // Handle file upload
    fileInput.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('file', file);
            
            try {
                const response = await fetch('upload-file.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();
                
                if (result.success) {
                    hasUploadedLetter = true;
                    reminderBox.style.display = 'none';
                    messageInput.disabled = false;
                    messageInput.placeholder = "Type your message...";
                }
            } catch (error) {
                console.error('Error uploading file:', error);
            }
        }
    });

    // Send message
    sendBtn.addEventListener('click', async () => {
        const message = messageInput.value.trim();
        if (message && hasUploadedLetter) {
            try {
                const response = await fetch('send-message.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        message: message,
                        conversation_id: conversationId // This should be set when page loads
                    })
                });
                
                if (response.ok) {
                    messageInput.value = '';
                    loadMessages();
                }
            } catch (error) {
                console.error('Error sending message:', error);
            }
        }
    });

    // Load messages periodically
    function loadMessages() {
        fetch(`get-messages.php?conversation_id=${conversationId}`)
            .then(response => response.json())
            .then(messages => {
                messageArea.innerHTML = messages.map(msg => `
                    <div class="message ${msg.sender_id === currentUserId ? 'sent' : 'received'}">
                        ${msg.file_path ? `<div class="file-attachment">
                            <i class="fas fa-file"></i>
                            <a href="${msg.file_path}" target="_blank">${msg.file_name}</a>
                        </div>` : ''}
                        <div class="message-content">${msg.message}</div>
                    </div>
                `).join('');
                messageArea.scrollTop = messageArea.scrollHeight;
            });
    }

    // Initial setup
    checkEmploymentLetter();
    loadMessages();
    setInterval(loadMessages, 5000); // Refresh messages every 5 seconds
});
