// Seat selection functionality
let selectedSeats = [];
const SEAT_PRICE = 300; // Price per seat

function toggleSeat(seatElement, seatNumber) {
    if (seatElement.classList.contains('booked')) {
        return;
    }
    
    if (seatElement.classList.contains('selected')) {
        seatElement.classList.remove('selected');
        selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
    } else {
        seatElement.classList.add('selected');
        selectedSeats.push(seatNumber);
    }
    
    updateSummary();
}

function updateSummary() {
    const summarySection = document.getElementById('booking-summary');
    if (!summarySection) return;
    
    const seatCount = selectedSeats.length;
    const totalPrice = seatCount * SEAT_PRICE;
    
    const html = `
        <div class="summary-row">
            <span class="summary-label">Selected Seats:</span>
            <span class="summary-value">${selectedSeats.join(', ') || 'None'}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Number of Seats:</span>
            <span class="summary-value">${seatCount}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Price per Seat:</span>
            <span class="summary-value">₹${SEAT_PRICE}</span>
        </div>
        <div class="summary-row total">
            <span class="summary-label">Total Amount:</span>
            <span class="summary-value">₹${totalPrice}</span>
        </div>
        <button type="button" class="btn btn-primary" style="width: 100%; margin-top: 15px;" 
                onclick="proceedToBooking()" ${seatCount === 0 ? 'disabled' : ''}>
            Proceed to Booking
        </button>
    `;
    
    summarySection.innerHTML = html;
}

function proceedToBooking() {
    if (selectedSeats.length === 0) {
        alert('Please select at least one seat');
        return;
    }
    
    const showId = document.getElementById('show-id')?.value;
    if (!showId) {
        alert('Invalid show selected');
        return;
    }
    
    // Store selected seats in form and submit
    const form = document.getElementById('booking-form');
    if (form) {
        document.getElementById('selected-seats').value = selectedSeats.join(',');
        form.submit();
    }
}

function initializeDatePicker() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        const today = new Date().toISOString().split('T')[0];
        input.min = today;
    });
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return true;
    
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.style.borderColor = '#dc3545';
            isValid = false;
        } else {
            input.style.borderColor = '#ddd';
        }
    });
    
    return isValid;
}

// Email validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Password strength checker
function checkPasswordStrength(password) {
    let strength = 0;
    if (password.length >= 8) strength++;
    if (/[a-z]/.test(password)) strength++;
    if (/[A-Z]/.test(password)) strength++;
    if (/[0-9]/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    return strength;
}

// Show alert message
function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) return;
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.textContent = message;
    
    alertContainer.innerHTML = '';
    alertContainer.appendChild(alertDiv);
    
    if (type === 'success') {
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeDatePicker();
    updateSummary();
});

// Movie search/filter functionality
function filterMovies(searchTerm) {
    const movies = document.querySelectorAll('.movie-card');
    
    movies.forEach(movie => {
        const title = movie.querySelector('.movie-title')?.textContent.toLowerCase() || '';
        const genre = movie.querySelector('.movie-genre')?.textContent.toLowerCase() || '';
        
        if (title.includes(searchTerm.toLowerCase()) || genre.includes(searchTerm.toLowerCase())) {
            movie.style.display = '';
        } else {
            movie.style.display = 'none';
        }
    });
}

// Print ticket
function printTicket() {
    window.print();
}

// Download ticket as PDF (simplified to HTML download)
function downloadTicket() {
    const element = document.getElementById('ticket-details');
    if (!element) return;
    
    const html = element.innerHTML;
    const blob = new Blob([html], { type: 'text/html' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'ticket.html';
    a.click();
}
