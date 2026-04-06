<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = isset($_SESSION['user_id']);
$userName = $isLoggedIn ? $_SESSION['user_name'] : '';
$userEmail = $isLoggedIn ? ($_SESSION['user_email'] ?? '') : '';
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="style.css">
    <title>Contact ByteBliss</title>
</head>
<body>
    

<main class="contact-hub">
    <div class="contact-header">
        <span class="mini-label-neon">CONNECT WITH US</span>
        <h1>Get in <span class="text-gradient">Touch</span></h1>
        <p>Have a question about a gadget? Want to claim an offer? We're here to help!</p>
    </div>

    <div class="contact-container">
        <div class="contact-form-card">
            <h3>Send a Message ✉️</h3>
            
            <form id="contactForm">
                <div class="input-group">
                    <input type="text" id="userName" required placeholder="Your Name">
                </div>
                <div class="input-group">
                    <input type="email" id="userEmail" required placeholder="Your Email">
                </div>
                <div class="input-group">
                    <select id="subject">
                        <option value="General Inquiry">General Inquiry</option>
                        <option value="Claim Offer">Claiming a Flash Deal</option>
                        <option value="Support">Tech Support</option>
                        <option value="Product Review">Product Review</option>
                        <option value="Suggestion">Suggestion</option>
                    </select>
                </div>

                <div class="rating-container">
                    <label>Your Rating:</label>
                    <div class="star-rating">
                        <input type="radio" name="rating" value="5" id="star5"><label for="star5">★</label>
                        <input type="radio" name="rating" value="4" id="star4"><label for="star4">★</label>
                        <input type="radio" name="rating" value="3" id="star3"><label for="star3">★</label>
                        <input type="radio" name="rating" value="2" id="star2"><label for="star2">★</label>
                        <input type="radio" name="rating" value="1" id="star1"><label for="star1">★</label>
                    </div>
                </div>
                
                <div class="input-group">
                    <textarea id="userMsg" rows="4" placeholder="How can ByteBliss help you today?"></textarea>
                </div>
                <button type="submit" class="send-btn" id="sendFeedbackBtn">Send Message ✨</button>
            </form>

            <div class="feedback-wall">
                <h4 class="accent-title">Community Feedback 💬</h4>
                <div class="feedback-grid" id="feedbackGrid">
                    <div class="loading-spinner">Loading feedback...</div>
                </div>
                <div id="loadMoreContainer" style="text-align: center;"></div>
            </div>

        </div>

        <div class="contact-info-panel">
            <div class="info-card-v3">
                <div class="icon-circle">⏰</div>
                <h4>Business Hours</h4>
                <p>Mon - Sat: 8:00 AM - 7:00 PM<br>Sun: Closed</p>
            </div>
            <div class="info-card-v3">
                <div class="icon-circle">📞</div>
                <h4>Call / WhatsApp</h4>
                <p>+254 700 000 000</p>
                <a href="https://wa.me/254707848095" class="whatsapp-link">Chat Now →</a>
            </div>

            <div class="info-card-v3 social-hub">
                <h4>Follow the Bliss 🚀</h4>
                <div class="social-icons-row">
                    <a href="https://instagram.com/ByteBliss_ke" class="social-icon-link">
                        <img src="https://cdn-icons-png.flaticon.com/512/174/174855.png" width="20"> Instagram
                    </a>
                    <a href="https://tiktok.com/ByteBliss_ke" class="social-icon-link">
                        <img src="https://cdn-icons-png.flaticon.com/512/3046/3046121.png" width="20"> TikTok
                    </a>
                    <a href="https://twitter.com/ByteBliss_ke" target="_blank" class="social-icon-link">
                        <img src="https://cdn-icons-png.flaticon.com/512/5968/5968958.png" alt="X"> Twitter
                    </a>
                </div>
            </div>
        </div>

        <section class="map-section-standalone">
    <div class="map-glass-card">
        <div class="map-header-flex">
            <h4 class="mini-label-neon">OUR LOCATION 📍</h4>
            <a href="https://www.google.com/maps/dir/?api=1&destination=-1.286389,36.817223" 
               target="_blank" 
               class="directions-btn">
               Get Directions 🚀
            </a>
        </div>
        
        <div id="glow-map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3988.8199!2d36.817223!3d-1.286389!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMcKwMTcnMTEuMCJTIDM2wrA0OScwMi4wIkU!5e0!3m2!1sen!2ske!4v1625000000000" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </div>
</section>
    </div>
</main>

<script>
// Global variables
let currentOffset = 0;
let loading = false;
let hasMore = true;

// Function to load feedback from database
function loadFeedback(append = false) {
    if (loading) return;
    loading = true;
    
    if (!append) {
        document.getElementById('feedbackGrid').innerHTML = '<div class="loading-spinner">Loading feedback...</div>';
    }
    
    fetch(`get_feedback.php?limit=6&offset=${currentOffset}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.feedback.length === 0) {
                    if (!append) {
                        document.getElementById('feedbackGrid').innerHTML = `
                            <div style="text-align: center; padding: 40px; color: #aaa;">
                                <p>✨ No feedback yet. Be the first to share your experience!</p>
                            </div>
                        `;
                    }
                    hasMore = false;
                    document.getElementById('loadMoreContainer').innerHTML = '';
                } else {
                    renderFeedback(data.feedback, append);
                    currentOffset += data.feedback.length;
                    hasMore = data.feedback.length === data.limit;
                    
                    // Show/hide load more button
                    if (hasMore && data.total > currentOffset) {
                        document.getElementById('loadMoreContainer').innerHTML = `
                            <button class="load-more-btn" onclick="loadMoreFeedback()">Load More →</button>
                        `;
                    } else {
                        document.getElementById('loadMoreContainer').innerHTML = '';
                    }
                }
            } else {
                console.error('Failed to load feedback');
                if (!append) {
                    document.getElementById('feedbackGrid').innerHTML = `
                        <div style="text-align: center; padding: 40px; color: #ff4757;">
                            <p>⚠️ Failed to load feedback. Please refresh the page.</p>
                        </div>
                    `;
                }
            }
            loading = false;
        })
        .catch(error => {
            console.error('Error:', error);
            loading = false;
            if (!append) {
                document.getElementById('feedbackGrid').innerHTML = `
                    <div style="text-align: center; padding: 40px; color: #ff4757;">
                        <p>⚠️ Connection error. Please try again.</p>
                    </div>
                `;
            }
        });
}

// Function to render feedback items
function renderFeedback(feedbackList, append) {
    const grid = document.getElementById('feedbackGrid');
    const feedbackHtml = feedbackList.map(feedback => `
        <div class="feedback-item" data-id="${feedback.id}">
            <div class="feedback-message">
                <p>"${escapeHtml(feedback.message)}"</p>
            </div>
            <div class="feedback-meta">
                <span class="feedback-name">${escapeHtml(feedback.full_name)}</span>
                <span class="feedback-date">${feedback.created_at}</span>
            </div>
            ${feedback.rating ? `
            <div class="feedback-rating">
                ${'★'.repeat(feedback.rating)}${'☆'.repeat(5 - feedback.rating)}
            </div>
            ` : ''}
        </div>
    `).join('');
    
    if (append) {
        grid.insertAdjacentHTML('beforeend', feedbackHtml);
    } else {
        grid.innerHTML = feedbackHtml;
    }
}

// Function to load more feedback
function loadMoreFeedback() {
    if (hasMore && !loading) {
        loadFeedback(true);
    }
}

// Function to submit feedback
function submitFeedback(event) {
    event.preventDefault();
    
    const name = document.getElementById('userName').value.trim();
    const email = document.getElementById('userEmail').value.trim();
    const subject = document.getElementById('subject').value;
    const message = document.getElementById('userMsg').value.trim();
    const rating = document.querySelector('input[name="rating"]:checked');
    
    // Validation
    if (!name || !email || !message) {
        alert('Please fill in your name, email, and message.');
        return;
    }
    
    if (message.length < 5) {
        alert('Message must be at least 5 characters long.');
        return;
    }
    
    // Disable button and show loading
    const submitBtn = document.getElementById('sendFeedbackBtn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Sending... ✨';
    
    // Prepare data
    const feedbackData = {
        name: name,
        email: email,
        subject: subject,
        message: message,
        rating: rating ? parseInt(rating.value) : null
    };
    
    // Send to server
    fetch('save_feedback.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(feedbackData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reset form
            document.getElementById('contactForm').reset();
            
            // Clear rating selection
            document.querySelectorAll('input[name="rating"]').forEach(r => r.checked = false);
            
            // Show success message
            alert(data.message);
            
            // Add new feedback to the top of the list
            if (data.feedback) {
                const grid = document.getElementById('feedbackGrid');
                const newFeedbackHtml = `
                    <div class="feedback-item" data-id="${data.feedback.id}" style="animation: fadeInUp 0.5s ease;">
                        <div class="feedback-message">
                            <p>"${escapeHtml(data.feedback.message)}"</p>
                        </div>
                        <div class="feedback-meta">
                            <span class="feedback-name">${escapeHtml(data.feedback.full_name)}</span>
                            <span class="feedback-date">Just now</span>
                        </div>
                        ${data.feedback.rating ? `
                        <div class="feedback-rating">
                            ${'★'.repeat(data.feedback.rating)}${'☆'.repeat(5 - data.feedback.rating)}
                        </div>
                        ` : ''}
                    </div>
                `;
                grid.insertAdjacentHTML('afterbegin', newFeedbackHtml);
                
                // Reset offset to refresh the list
                currentOffset = 0;
                hasMore = true;
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to send feedback. Please check your connection and try again.');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Helper function to escape HTML
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Add animation style
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .feedback-item {
        animation: fadeInUp 0.3s ease;
    }
`;
document.head.appendChild(style);

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Load feedback from database
    loadFeedback();
    
    // Attach form submit handler
    const form = document.getElementById('contactForm');
    if (form) {
        form.addEventListener('submit', submitFeedback);
    }
});
</script>


</body>
</html>