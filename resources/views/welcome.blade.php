<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CommunityConnect Chat</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap');
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #1a202c;
        }
        
        .chat-container {
            width: 100%;
            max-width: 600px;
            height: 90vh;
            display: flex;
            flex-direction: column;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .chat-header {
            background-color: #4c51bf;
            color: #fff;
            padding: 16px;
            text-align: center;
            font-weight: 700;
            font-size: 1.25rem;
            border-bottom: 2px solid #3c4199;
        }
        
        .message-list {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .message-list::-webkit-scrollbar {
            width: 8px;
        }
        
        .message-list::-webkit-scrollbar-thumb {
            background-color: #cbd5e0;
            border-radius: 4px;
        }

        .message-bubble {
            display: flex;
            flex-direction: column;
            padding: 12px;
            border-radius: 12px;
            max-width: 75%;
            word-wrap: break-word;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }
        
        .message-bubble.sent {
            align-self: flex-end;
            background-color: #4c51bf;
            color: #fff;
            border-bottom-right-radius: 4px;
        }
        
        .message-bubble.received {
            align-self: flex-start;
            background-color: #e2e8f0;
            color: #1a202c;
            border-bottom-left-radius: 4px;
        }

        .message-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-top: 8px;
        }
        
        .file-preview {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            margin-top: 10px;
            background-color: #f7fafc;
        }

        .file-preview .file-icon {
            font-size: 2rem;
            color: #4c51bf;
        }

        .file-preview .file-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .file-preview .file-info .file-name {
            font-weight: 500;
        }

        .file-preview .file-info .file-size {
            font-size: 0.85rem;
            color: #718096;
        }
        
        .message-input-area {
            padding: 16px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 8px;
            align-items: center;
        }
        
        .message-input {
            flex-grow: 1;
            padding: 12px;
            border: 1px solid #cbd5e0;
            border-radius: 20px;
            outline: none;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .message-input:focus {
            border-color: #4c51bf;
        }
        
        .send-button, .file-button {
            background-color: #4c51bf;
            color: #fff;
            border: none;
            padding: 12px;
            border-radius: 50%;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .send-button:hover, .file-button:hover {
            background-color: #3c4199;
            transform: scale(1.05);
        }

        .send-button:disabled {
            background-color: #a0aec0;
            cursor: not-allowed;
            transform: none;
        }

        .message-input-area .file-preview-container {
            display: flex;
            flex-direction: column;
            padding: 10px;
            border: 1px solid #cbd5e0;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .file-preview-container img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 8px;
        }

        .message-input-area .file-info {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        /* Responsive styles */
        @media (max-width: 640px) {
            .chat-container {
                height: 100vh;
                border-radius: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<div class="chat-container">
    <div class="chat-header">
        CommunityConnect Bot
    </div>

    <div class="message-list" id="message-list">
        <div class="message-bubble received">
            <div class="message-content">Hello, I'm the CommunityConnect bot. How can I assist you with your report?</div>
        </div>
    </div>

    <div class="message-input-area">
        <input type="text" id="message-input" class="message-input" placeholder="Type your message...">
        <input type="file" id="file-input" hidden>
        <button id="file-button" class="file-button" title="Attach file">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-paperclip">
                <path d="M21.44 14.83l-6.85 6.85a3 3 0 0 1-4.24 0l-1.5-1.5a3 3 0 0 1 0-4.24l6.85-6.85a3 3 0 0 1 4.24 0l1.5 1.5a3 3 0 0 1 0 4.24l-6.85 6.85a3 3 0 0 1-4.24 0l-1.5-1.5a3 3 0 0 1 0-4.24l6.85-6.85a3 3 0 0 1 4.24 0l1.5 1.5a3 3 0 0 1 0 4.24z"></path>
            </svg>
        </button>
        <button id="send-button" class="send-button" title="Send message">
            <svg xmlns="http://www.3c.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-send">
                <line x1="22" y1="2" x2="11" y2="13"></line>
                <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
            </svg>
        </button>
    </div>
</div>

<script>
    const messageList = document.getElementById('message-list');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const fileInput = document.getElementById('file-input');
    const fileButton = document.getElementById('file-button');

    let uploadedFile = null;

    // A function to display a message on the UI
    function displayMessage(text, isSent, file = null) {
        const messageBubble = document.createElement('div');
        messageBubble.classList.add('message-bubble', isSent ? 'sent' : 'received');

        const content = document.createElement('div');
        content.classList.add('message-content');

        if (file) {
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = file.dataUrl;
                content.appendChild(img);
            } else {
                // Display a document icon and file name
                const docIcon = document.createElement('div');
                docIcon.classList.add('file-icon');
                docIcon.innerHTML = `
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <line x1="10" y1="9" x2="8" y2="9"></line>
                    </svg>
                `;
                const fileName = document.createElement('span');
                fileName.textContent = file.name;
                content.appendChild(docIcon);
                content.appendChild(fileName);
                content.style.flexDirection = 'row';
                content.style.alignItems = 'center';
                content.style.gap = '10px';
            }
        }
        
        if (text) {
            const textContent = document.createElement('div');
            textContent.textContent = text;
            content.appendChild(textContent);
        }

        messageBubble.appendChild(content);
        messageList.appendChild(messageBubble);

        // Scroll to the bottom
        messageList.scrollTop = messageList.scrollHeight;
    }

    // Function to handle sending a message
    function sendMessage() {
        const messageText = messageInput.value.trim();

        if (messageText === '' && !uploadedFile) {
            return;
        }

        displayMessage(messageText, true, uploadedFile);
        
        // Clear inputs after sending
        messageInput.value = '';
        uploadedFile = null;

        // Simulate a response from the AI
        setTimeout(() => {
            if (uploadedFile) {
                const mimeType = uploadedFile.type.split('/')[0];
                let responseText = `Thanks for sending the ${mimeType} file. I will analyze it and get back to you with a report.`;
                if (mimeType === 'image') {
                    responseText = "Thank you for the photo. My computer vision model will now analyze the scene and categorize the report.";
                } else if (mimeType === 'application') {
                    responseText = "Thanks for the document. I will extract key information and attach it to your report.";
                }
                displayMessage(responseText, false);
            } else {
                displayMessage("Thank you for your message. A ticket has been created with your report.", false);
            }
        }, 1500);
    }

    // Event listeners
    sendButton.addEventListener('click', sendMessage);

    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });

    fileButton.addEventListener('click', () => {
        fileInput.click();
    });

    fileInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            uploadedFile = {
                name: file.name,
                type: file.type,
                size: file.size,
            };

            // Read file as data URL for image previews
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    uploadedFile.dataUrl = e.target.result;
                    sendMessage();
                };
                reader.readAsDataURL(file);
            } else {
                sendMessage();
            }
        }
    });
</script>

</body>
</html>
