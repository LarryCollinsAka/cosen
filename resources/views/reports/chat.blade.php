@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white text-center">
                    <h5 class="mb-0">AI-Powered Incident Reporter</h5>
                </div>
                <div class="card-body">
                    <div id="initial-form-container">
                        <h4 class="text-center mb-4">Start a new report</h4>
                        <form id="initial-report-form">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="image" class="form-label">Image (Optional)</label>
                                <input type="file" class="form-control" id="image" name="image">
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="submit-initial-btn">Submit Report</button>
                            </div>
                        </form>
                    </div>

                    <div id="chat-container" style="display: none;">
                        <div id="messages-container" class="bg-light p-3 border rounded mb-3" style="min-height: 250px; max-height: 400px; overflow-y: auto;">
                            </div>
                        <form id="chat-form">
                            @csrf
                            <div class="input-group">
                                <input type="text" class="form-control" id="chat-input" placeholder="Type your message..." required>
                                <button class="btn btn-primary" type="submit">Send</button>
                            </div>
                            <input type="hidden" id="incident-id">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const initialForm = document.getElementById('initial-report-form');
        const initialFormContainer = document.getElementById('initial-form-container');
        const chatContainer = document.getElementById('chat-container');
        const messagesContainer = document.getElementById('messages-container');
        const chatForm = document.getElementById('chat-form');
        const chatInput = document.getElementById('chat-input');
        const incidentIdInput = document.getElementById('incident-id');

        // Handles the initial report submission
        initialForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(initialForm);

            try {
                const response = await fetch('{{ route("reports.store") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                });

                const data = await response.json();

                if (data.success) {
                    // Hide the initial form and show the chat
                    initialFormContainer.style.display = 'none';
                    chatContainer.style.display = 'block';

                    // Save the incident ID
                    incidentIdInput.value = data.incident_id;

                    // Display the AI's first message
                    addMessage('assistant', data.initial_message);
                } else {
                    alert(data.error || 'Something went wrong.');
                }
            } catch (error) {
                console.error('Error submitting initial report:', error);
                alert('An error occurred. Please try again.');
            }
        });

        // Handles messages sent during the chat
        chatForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const userMessage = chatInput.value.trim();
            if (!userMessage) return;

            addMessage('user', userMessage);
            chatInput.value = '';

            const incidentId = incidentIdInput.value;

            try {
                const response = await fetch('/reports/continue-conversation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        incident_id: incidentId,
                        message: userMessage
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    addMessage('assistant', data.ai_message);
                } else {
                    addMessage('assistant', 'Sorry, I am unable to process that. Please try again.');
                }
            } catch (error) {
                console.error('Error in chat conversation:', error);
                addMessage('assistant', 'An error occurred. Please try again later.');
            }
        });

        function addMessage(role, message) {
            const messageElement = document.createElement('div');
            const alignmentClass = role === 'user' ? 'text-end' : 'text-start';
            const messageStyleClass = role === 'user' ? 'bg-primary text-white' : 'bg-secondary text-white';

            messageElement.classList.add('mb-2', alignmentClass);
            messageElement.innerHTML = `<span class="d-inline-block rounded-pill py-2 px-3 ${messageStyleClass}">${message}</span>`;
            messagesContainer.appendChild(messageElement);
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    });
</script>
@endsection