<div id="payments-modal" class="checkout-overlay">
    <div class="payment-container glass">
        
        <div class="modal-header">
            <h2 class="cyan-text"><i class="fas fa-lock"></i> Secure Checkout</h2>
            <button class="auth-close" onclick="closePaymentModal()">&times;</button>
        </div>

        <div class="payment-grid">
            <div class="payment-form-section">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" id="pay-name" class="modern-input" placeholder="e.g. John Doe">
                </div>

                <div class="input-group">
                    <label>Phone Number *</label>
                    <input type="tel" id="pay-phone" class="modern-input" placeholder="e.g. 0712345678" required>
                    <small style="color: #888; font-size: 0.7rem;">We'll use this to contact you about your order</small>
                </div>
                <div class="input-group">
                    <label>County *</label>
                    <select id="pay-county" class="modern-input" required>
                        <option value="">Select your county</option>
                        <option value="Nairobi">Nairobi</option>
                        <option value="Kiambu">Kiambu</option>
                        <option value="Nakuru">Nakuru</option>
                        <option value="Kajiado">Kajiado</option>
                        <option value="Machakos">Machakos</option>
                        <option value="Mombasa">Mombasa</option>
                        <option value="Kisumu">Kisumu</option>
                        <option value="Uasin Gishu">Uasin Gishu (Eldoret)</option>
                        <option value="Meru">Meru</option>
                        <option value="Nyeri">Nyeri</option>
                        <option value="Kilifi">Kilifi</option>
                        <option value="Kwale">Kwale</option>
                        <option value="Laikipia">Laikipia</option>
                        <option value="Kakamega">Kakamega</option>
                        <option value="Bungoma">Bungoma</option>
                        <option value="Busia">Busia</option>
                        <option value="Trans Nzoia">Trans Nzoia</option>
                        <option value="Vihiga">Vihiga</option>
                        <option value="Siaya">Siaya</option>
                        <option value="Homa Bay">Homa Bay</option>
                        <option value="Migori">Migori</option>
                        <option value="Kisii">Kisii</option>
                        <option value="Nyamira">Nyamira</option>
                        <option value="Kericho">Kericho</option>
                        <option value="Bomet">Bomet</option>
                        <option value="Narok">Narok</option>
                        <option value="Samburu">Samburu</option>
                        <option value="Isiolo">Isiolo</option>
                        <option value="Marsabit">Marsabit</option>
                        <option value="Turkana">Turkana</option>
                        <option value="West Pokot">West Pokot</option>
                        <option value="Elgeyo Marakwet">Elgeyo Marakwet</option>
                        <option value="Baringo">Baringo</option>
                        <option value="Taita Taveta">Taita Taveta</option>
                        <option value="Tana River">Tana River</option>
                        <option value="Lamu">Lamu</option>
                        <option value="Garissa">Garissa</option>
                        <option value="Wajir">Wajir</option>
                        <option value="Mandera">Mandera</option>
                        <option value="Tharaka Nithi">Tharaka Nithi</option>
                        <option value="Embu">Embu</option>
                        <option value="Kirinyaga">Kirinyaga</option>
                        <option value="Murang'a">Murang'a</option>
                        <option value="Nyandarua">Nyandarua</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Delivery Address</label>
                    <textarea id="address" class="modern-input" rows="2" placeholder="e.g. Kenyatta Avenue, Building Name, Floor, Apartment/House Number" required></textarea>
                </div>

                <div class="input-group">
                    <label>Delivery Instructions (Optional)</label>
                    <textarea id="delivery-instructions" class="modern-input" rows="2" placeholder="e.g. Call before delivery, Gate code, Landmark"></textarea>
                </div>

                <label class="cyan-text" style="display: block; margin-top: 20px;">Payment Method</label>
                <div class="method-selector">
                    <div class="method-card active" id="method-mpesa" onclick="selectMethod('mpesa')">
                        <div class="method-icon"><i class="fas fa-mobile-alt"></i></div> <div class="method-details">
                            <span class="method-title">M-Pesa</span>
                            <span class="method-sub">Pay via STK push</span>
                        </div>
                        <div class="method-tick"><i class="fas fa-check-circle"></i></div>
                    </div>

                    <div class="method-card" id="method-cod" onclick="selectMethod('cod')">
                        <div class="method-icon"><i class="fas fa-truck"></i></div>
                        <div class="method-details">
                            <span class="method-title">Cash on Delivery</span>
                            <span class="method-sub">Pay when you receive</span>
                        </div>
                        <div class="method-tick"><i class="fas fa-check-circle"></i></div>
                    </div>
                </div>

                <div id="mpesa-fields" class="mpesa-container">
                    <div class="input-group">
                        <label>M-Pesa Number</label>
                        <div class="phone-input-wrapper">
                            <span class="prefix">+254</span>
                            <input type="number" id="mpesa-phone" class="modern-input" placeholder="712345678">
                        </div>
                        <small style="color: #888; font-size: 0.7rem;">You'll receive a payment prompt on this number</small>
                    </div>
                </div>
            </div>

            <div class="order-summary-sidebar glass">
                <h3>Order Summary</h3>
                <div id="summary-items" class="custom-scrollbar">
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span id="final-total" class="cyan-text">KSh 0</span>
                </div>
                <div class="delivery-note" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.75rem; color: #888;">
                    <p>🚚 Delivery within 1-4 business days</p>
                    <p>💰 Free delivery for orders over KSh 50,000</p>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button class="btn-checkout-final" id="place-order-btn" onclick="processOrder()">
                <span id="btn-text">Confirm & Place Order &rarr;</span>
                <div class="spinner" id="payment-spinner"></div>
            </button>
        </div>
    </div>
</div>

<script src="interactions.js"></script>