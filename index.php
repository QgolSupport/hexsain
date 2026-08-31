<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Corrugated Containers</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* ===== Global Styles ===== */
		
		.hidden { display: none; } /* Honeypot field */
        .error { color: red; }

        
        :root {
            --primary: #2E7D32; /* Eco green */
            --secondary: #1B5E20; /* Dark green */
            --accent: #FFC107; /* Gold */
            --light: #F5F5F5;
            --dark: #333;
            --text: #424242;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .btn {
            display: inline-block;
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .section-title {
            font-size: clamp(1.75rem, 3vw, 2.5rem);
            color: var(--primary);
            margin-bottom: 30px;
            text-align: center;
            position: relative;
        }

        .section-title:after {
            content: '';
            display: block;
            width: 80px;
            height: 4px;
            background: var(--accent);
            margin: 15px auto;
        }

        /* ===== Header ===== */
        header {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
            width: 100%;
            z-index: 1000;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 0;
        }

        .logo {
            display: flex;
            align-items: center;
        }

        .logo img {
            height: 50px;
            margin-right: 10px;
        }

        .logo-text {
            font-weight: 700;
            color: var(--primary);
        }

        .logo-text span {
            display: block;
            font-size: 0.8rem;
            color: var(--text);
            font-weight: 400;
        }

        /* Desktop Navigation */
        .desktop-nav {
            display: none;
        }

        .desktop-nav ul {
            display: flex;
            list-style: none;
        }

        .desktop-nav li {
            margin-left: 30px;
            position: relative;
        }

        .desktop-nav a {
            text-decoration: none;
            color: var(--dark);
            font-weight: 600;
            transition: var(--transition);
            padding: 5px 0;
        }

        .desktop-nav a:hover {
            color: var(--primary);
        }

        .desktop-nav a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            background: var(--primary);
            bottom: 0;
            left: 0;
            transition: var(--transition);
        }

        .desktop-nav a:hover:after {
            width: 100%;
        }

        /* Mobile Navigation */
        .mobile-nav-toggle {
            display: block;
            font-size: 1.5rem;
            color: var(--primary);
            cursor: pointer;
            background: none;
            border: none;
        }

        .mobile-nav {
            position: fixed;
            top: 80px;
            left: 0;
            width: 100%;
            height: calc(100vh - 80px);
            background: white;
            padding: 20px;
            transform: translateX(100%);
            transition: var(--transition);
            z-index: 999;
            overflow-y: auto;
        }

        .mobile-nav.active {
            transform: translateX(0);
        }

        .mobile-nav ul {
            list-style: none;
        }

        .mobile-nav li {
            margin-bottom: 20px;
        }

        .mobile-nav a {
            text-decoration: none;
            color: var(--dark);
            font-size: 1.2rem;
            font-weight: 600;
        }

        /* ===== Hero Section ===== */
        .hero {
            padding: 120px 0 60px;
            background: linear-gradient(rgba(255,255,255,0.9), rgba(255,255,255,0.9)), 
                        url('https://images.unsplash.com/photo-1605000797499-95a51c5269ae?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1471&q=80');
            background-size: cover;
            background-position: center;
        }

        .hero-content {
            max-width: 600px;
        }

        .hero h1 {
            font-size: clamp(2rem, 5vw, 3rem);
            color: var(--primary);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .hero p {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        /* ===== Highlights Section ===== */
        .highlights {
            padding: 60px 0;
            background: var(--light);
        }

        .highlight-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .highlight-card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: var(--transition);
        }

        .highlight-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.1);
        }

        .highlight-icon {
            font-size: 2.5rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .highlight-card h3 {
            margin-bottom: 15px;
            color: var(--dark);
        }

        /* ===== Products Section ===== */
        .products {
            padding: 60px 0;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
        }

        .product-card {
            border: 1px solid #eee;
            border-radius: 8px;
            overflow: hidden;
            transition: var(--transition);
        }

        .product-card:hover {
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .product-img {
            height: 200px;
            overflow: hidden;
        }

        .product-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .product-card:hover .product-img img {
            transform: scale(1.05);
        }

        .product-info {
            padding: 20px;
        }

        .product-info h3 {
            margin-bottom: 10px;
            color: var(--primary);
        }

        .product-info p {
            margin-bottom: 15px;
            font-size: 0.9rem;
        }

        /* ===== RFQ Section ===== */
        .rfq {
            padding: 60px 0;
            background: #f9f9f9;
        }

        .rfq-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .rfq-info {
            margin-bottom: 30px;
        }

        .rfq-info h3 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .rfq-info ul {
            list-style: none;
            margin-bottom: 30px;
        }

        .rfq-info li {
            margin-bottom: 15px;
            padding-left: 25px;
            position: relative;
        }

        .rfq-info li:before {
            content: "✓";
            color: var(--primary);
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .rfq-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .rfq-form h3 {
            color: var(--primary);
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .form-submit {
            text-align: center;
        }

        /* ===== About Section ===== */
        .about {
            padding: 60px 0;
            background: var(--light);
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
            align-items: center;
        }

        .about-text h2 {
            color: var(--primary);
            margin-bottom: 20px;
        }

        .about-text p {
            margin-bottom: 15px;
        }

        .about-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 30px;
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            background: white;
            border-radius: 8px;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            color: var(--text);
        }

        /* ===== Contact Section ===== */
        .contact {
            padding: 60px 0;
        }

        .contact-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 40px;
        }

        .contact-info {
            margin-bottom: 30px;
        }

        .contact-info h3 {
            color: var(--primary);
            margin-bottom: 20px;
        }

        .contact-details {
            margin-bottom: 20px;
        }

        .contact-details div {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .contact-details i {
            margin-right: 15px;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .contact-form input,
        .contact-form textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .contact-form textarea {
            height: 150px;
            resize: vertical;
        }

        /* ===== Footer ===== */
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 0 20px;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            margin-bottom: 40px;
        }

        .footer-col h3 {
            color: white;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-col h3:after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 40px;
            height: 2px;
            background: var(--accent);
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col li {
            margin-bottom: 10px;
        }

        .footer-col a {
            color: #ccc;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-col a:hover {
            color: white;
            padding-left: 5px;
        }

        .social-links {
            display: flex;
            gap: 15px;
        }

        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            color: white;
            transition: var(--transition);
        }

        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .copyright {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 0.9rem;
            color: #aaa;
        }

        /* ===== Mobile Sticky CTA ===== */
        .mobile-cta {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--primary);
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            z-index: 99;
            transition: var(--transition);
            text-decoration: none;
        }

        .mobile-cta:hover {
            background: var(--secondary);
            transform: scale(1.1);
        }

        /* ===== Media Queries ===== */
        @media (min-width: 768px) {
            .mobile-nav-toggle {
                display: none;
            }

            .desktop-nav {
                display: block;
            }

            .about-content {
                grid-template-columns: 1fr 1fr;
            }

            .contact-container {
                grid-template-columns: 1fr 1fr;
            }

            .rfq-container {
                grid-template-columns: 1fr 1fr;
            }
            
            .form-row {
                grid-template-columns: 1fr 1fr;
            }

            .hero {
                padding: 150px 0 80px;
            }
        }

        @media (min-width: 992px) {
            .hero {
                padding: 180px 0 100px;
            }

            .hero h1 {
                font-size: 3.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <img src="Images/logo.jpg" alt="Safe Corrugated Containers Logo">
                <div class="logo-text">
                    Safe Corrugated Containers Pvt. Ltd.
                    <span>Sustainable Packaging Since 1992</span>
                </div>
            </div>

            <nav class="desktop-nav">
                <ul>
                    <li><a href="#home">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="#products">Products</a></li>
                    <li><a href="#rfq">Request Quote</a></li>
                    <li><a href="#contact">Contact</a></li>
                </ul>
            </nav>

            <button class="mobile-nav-toggle" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <nav class="mobile-nav">
            <ul>
                <li><a href="#home">Home</a></li>
                <li><a href="#about">About</a></li>
                <li><a href="#products">Products</a></li>
                <li><a href="#rfq">Request Quote</a></li>
                <li><a href="#contact">Contact</a></li>
            </ul>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="hero" id="home">
        <div class="container">
            <div class="hero-content">
                <h1>Eco-Friendly Packaging Solutions</h1>
                <p>For over 30 years, we've provided sustainable packaging to businesses. Our 100% recyclable solutions protect your products while protecting the planet.</p>
                <a href="#rfq" class="btn">Request a Quote</a>
            </div>
			
		<!-- 	<div class="product-features">
            <br>
            <ul>
                <li><strong>Recyclable:</strong> The honeycomb wrapping paper is the green alternative to unsustainable plastic wrap and is 100% 
				            paper based. Honeycomb cells interlock themselves & eliminates the need for other packaging. Its easy to tear at the 
							exact length needed. Aesthetically pleasing packaging provides a better un-boxing experience.</li>
                <li><strong>Improved Protection:</strong> An eco-friendly alternative to bubble packing wrap ensures fragile items 
				            arrive undamaged. It is also more environment friendly and at the same time, can further reduce the packaging cost.</li>
                <li><strong>Versatile Usage:</strong> It can be used to wrap dishes, photo frames, glasswares, vases, cups, plates, silverware and 
				            literally everything. Better protection while moving/transportation. Stays intact and safe while delivering goods! 
							It is paper-sturdy, versatile, fits around weird shaped pieces, way better than any foam sheets.</li>
				<li><strong>Excellent Cushioning: </strong>Perfect for wrapping and Protecting fragile items & first class shipping.
							Its made of high-quality strong and shock-absorbent kraft paper, odourless, green eco-friendly,
							reusable and lightweight, surpassing traditional wrapping solutions, a great alternative to plastic sheets, ideal
							packaging paper. It has a unique honeycomb structure that can wrap items tightly to provide strong
							shock absorption and protection for packaged products, avoids your items from cracking, chipping and
							damaged during transportation.</li>
				<li><strong>Easy to Use and Tear: </strong>The packaging paper roll is easy to use and tear to any length you want
							without tools required. After the honeycomb paper is stretched and unfolded,
							it forms into a honeycomb shaped pattern which gives a professional and a delicate look to your products.</li>
				<li><strong>Improve Packaging Efficiency: </strong>The packaging paper can reduce the accumulation of plastic and does not
							take up additional space in packing boxes. It also save your packaging/shipping cost and simplifies
							the packing process.</li>
            </ul>
           </div> -->
        </div>
    </section>

    <!-- Highlights Section -->
    <section class="highlights">
        <div class="container">
            <div class="highlight-grid">
                <div class="highlight-card">
                    <div class="highlight-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3>30+ Years Experience</h3>
                    <p>Trusted by Major Indian and International companies since 1992</p>
                </div>

                <div class="highlight-card">
                    <div class="highlight-icon">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <h3>100% Recyclable</h3>
                    <p>Zero plastic packaging solutions</p>
                </div>

                <div class="highlight-card">
                    <div class="highlight-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3>Custom Solutions</h3>
                    <p>Tailored to your specific needs</p>
                </div>

                <div class="highlight-card">
                    <div class="highlight-icon">
                        <i class="fas fa-globe"></i>
                    </div>
                    <h3>Global Supply</h3>
                    <p>Reliable international shipping</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products" id="products">
        <div class="container">
            <h2 class="section-title">Our Sustainable Products</h2>
            <div class="product-grid">
                <div class="product-card">
                    <div class="product-img">
                        <img src="/Images/cartonBox.jpg" alt="Corrugated Cartons">
                    </div>
                    <div class="product-info">
                        <h3>Corrugated Cartons</h3>
                        <p>Durable, lightweight, and customizable packaging for all industries.</p>
                        <a href="#rfq" class="btn">Get Quote</a>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-img">
                        <img src="/Images/paper_hc.jpg" alt="Paper Honeycomb">
                    </div>
                    <div class="product-info">
                        <h3>Paper Honeycomb</h3>
                        <p>High-strength, shock-absorbent sustainable alternative to foam.</p>
                        <a href="#rfq" class="btn">Get Quote</a>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-img">
                        <img src="/Images/EdgeProtector.jpg" alt="Paper Angles">
                    </div>
                    <div class="product-info">
                        <h3>Slotted Angles & Edge Protectors</h3>
                        <p>Protect fragile items during shipping with our recyclable solutions.</p>
                        <a href="#rfq" class="btn">Get Quote</a>
                    </div>
                </div>

                <div class="product-card">
                    <div class="product-img">
                        <img src="Images/papertubes.jpg" alt="Spiral Tubes">
                    </div>
                    <div class="product-info">
                        <h3>Spiral Wound Paper Tubes</h3>
                        <p>Sturdy cylindrical packaging for industrial and textile yarn applications.</p>
                        <a href="#rfq" class="btn">Get Quote</a>
                    </div>
                </div>
				
				<div class="product-card">
                    <div class="product-img">
                        <img src="Images/paallet.jpg" alt="pallet">
                    </div>
                    <div class="product-info">
                        <h3>Pallet</h3>
                        <p>Flat & rigid platform with load carrying capacity upto 2tons and are exempt from ISPM 15 norms for exports.</p>
                        <a href="#rfq" class="btn">Get Quote</a>
                    </div>
                </div>
				
				<div class="product-card">
                    <div class="product-img">
                        <img src="Images/bubblewrapping.jpg" alt="bubbleWrap">
                    </div>
                    <div class="product-info">
                        <h3>Bubble Wrap</h3>
                        <p>Ecofriendly alternate to plastic bubble wrap.</p>
                        <a href="#rfq" class="btn">Get Quote</a>
                    </div>
                </div>
				
            </div>
        </div>
    </section>

    <!-- RFQ Section -->
    <section class="rfq" id="rfq">
        <div class="container">
            <h2 class="section-title">Request a Quote</h2>
            <div class="rfq-container">
                <div class="rfq-info">
                    <h3>Get Custom Pricing for Your Business</h3>
                    <p>Complete this form to receive a competitive quote. Our packaging specialists will provide tailored solutions for your requirements.</p>
                    
                    <ul>
                        <li>Volume discounts for bulk orders</li>
                        <li>Custom sizes and printing options</li>
                        <li>Dedicated account manager</li>
                        <li>Fast turnaround times</li>
                    </ul>
                    
                    <div class="contact-details">
                        <div>
                            <i class="fas fa-phone"></i>
                            <span>For urgent inquiries:+91 9894086315</span>
                        </div>
                        <div>
                            <i class="fas fa-envelope"></i>
                            <span>Email: marketing.hexsa@gmail.com</span>
                        </div>
                        <div>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>SAFE CORRUGATED CONTAINERS PVT. LTD. <br> No. 12-15, Gopal Reddy Kandigai, Gummidipoondi,<br>  Chennai - 601 201, <br>  Tamil Nadu, India</span>
                        </div>
                    </div>
                </div>
				
                
                <div class="rfq-form">
                    <h3>RFQ Form</h3>
                    <form id="quoteForm" action="send-email.php" method="POST">
						<div class="hidden" aria-hidden="true">
							<label for="quoteWebsite">Leave this empty:</label>
							<input type="text" id="quoteWebsite" name="website" tabindex="-1" autocomplete="off">
						</div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="company">Company Name*</label>
                                <input type="text" id="company" name="company" required>
                            </div>
                            <div class="form-group">
                                <label for="name">Contact Person*</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">Email*</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone*</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="product">Product Interest*</label>
                            <select id="product" name="product" required>
                                <option value="">Select Product</option>
                                <option value="corrugated">Corrugated Cartons</option>
                                <option value="honeycomb">Paper Honeycomb</option>
                                <option value="angles">Paper Angles/Edge Protectors</option>
                                <option value="tubes">Spiral Wound Tubes</option>
								<option value="pallet">Pallet</option>
                                <option value="bubblewrap">Honeycomb Bubble Wrap</option>
                                <option value="custom">Custom Solution</option>
                            </select>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="quantity">Estimated Monthly Quantity*</label>
                                <select id="quantity" name="quantity" required>
                                    <option value="">Select Range</option>
                                    <option value="1k">1,000 - 5,000 units</option>
                                    <option value="5k">5,001 - 20,000 units</option>
                                    <option value="20k">20,001 - 50,000 units</option>
                                    <option value="50k">50,001 - 100,000 units</option>
                                    <option value="100k">100,000+ units</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="urgency">Urgency</label>
                                <select id="urgency" name="urgency">
                                    <option value="standard">Standard (5-7 days)</option>
                                    <option value="rush">Rush (3-5 days)</option>
                                    <option value="emergency">Emergency (1-2 days)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="specs">Special Requirements</label>
                            <textarea id="specs" name="specs" placeholder="Dimensions, materials, printing, etc."></textarea>
                        </div>
						
                        <div class="form-submit">
                            <button type="submit" class="btn">Request Quote</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section class="about" id="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2 class="section-title">About Our Company</h2>
                    <p>Founded in 1992, Safe Corrugated Containers has been at the forefront of sustainable packaging innovation. We combine three decades of expertise with cutting-edge manufacturing to deliver eco-friendly solutions that don't compromise on protection.</p>
                    <p>Our state-of-the-art facility in Tamil Nadu produces over 5,000 tons of eco-friendly packaging annually, serving industries from e-commerce to automotive.</p>
                    
                    <div class="about-stats">
                        <div class="stat-item">
                            <div class="stat-number">30+</div>
                            <div class="stat-label">Years Experience</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">100+</div>
                            <div class="stat-label">Clients Worldwide</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">100%</div>
                            <div class="stat-label">Recyclable</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">State Of The Art Machinery</div>
                            <div class="stat-label">Technology</div>
                        </div>
                    </div>
                </div>
                <div class="about-img">
                    <img src="Images/office.jpg" alt="Our Factory">
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact" id="contact">
        <div class="container">
            <h2 class="section-title">Get In Touch</h2>
            <div class="contact-container">
                <div class="contact-info">
                    <h3>Contact Details</h3>
                    <div class="contact-details">
                        <div>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>SAFE CORRUGATED CONTAINERS PVT. LTD. <br> No. 12-15, Gopal Reddy Kandigai, Gummidipoondi,<br>  Chennai - 601 201, <br>  Tamil Nadu, India</span>
                        </div>
                        <div>
                            <i class="fas fa-phone"></i>
                            <span>+91 9894086315</span>
                        </div>
                        <div>
                            <i class="fas fa-envelope"></i>
                            <span>marketing.hexsa@gmail.com</span>
                        </div>
                        <div>
                            <i class="fas fa-clock"></i>
                            <span>Mon-Sat: 9AM-5PM</span>
                        </div>
                    </div>
                    <!-- <h3>Follow Us</h3>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div> -->
                </div>

                <div class="contact-form">
                    <form id="contactForm"  action="send-email.php" method="POST">
					      <!-- Honeypot Field (Bots will fill this) -->
						<div class="hidden">
							<label for="website">Leave this empty:</label>
							<input type="text" id="website" name="website">
						</div>
						<div>
							<label for="name">Name*:</label>
							<input type="text" id="name" name="name" required minlength="2">
							<span id="nameError" class="error"></span>
						</div>

						<div>
							<label for="email">Email*:</label>
							<input type="email" id="email" name="email" required>
							<span id="emailError" class="error"></span>
						</div>
						
						<div>
							<label for="phone">Phone Number*:</label>
							<input type="phone" id="phone" name="phone" required minlength="10">
							<span id="phoneError" class="error"></span>
						</div>

						<div>
							<label for="message">Message*:</label>
							<textarea id="message" name="message" required minlength="10"></textarea>
							<span id="messageError" class="error"></span>
						</div>

						<button type="submit" class="btn">Send Message</button>
						
                       <!-- <input type="text" name="name" placeholder="Your Name" required>
                        <input type="email" name="email" placeholder="Your Email" required>
                        <input type="tel" name="phone" placeholder="Phone Number">
                        <textarea name="message" placeholder="Your Message" required></textarea>
                        <button type="submit" class="btn">Send Message</button> -->
						
						
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h3>Safe Corrugated</h3>
                    <p>Leading manufacturer of sustainable packaging solutions since 1992. Committed to quality, innovation, and environmental responsibility.</p>
                </div>

                <div class="footer-col">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#home">Home</a></li>
                        <li><a href="#about">About Us</a></li>
                        <li><a href="#products">Products</a></li>
                        <li><a href="#rfq">Request Quote</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Products</h3>
                    <ul>
                        <li><a href="#products">Corrugated Cartons</a></li>
                        <li><a href="#products">Paper Honeycomb</a></li>
                        <li><a href="#products">Edge Protectors</a></li>
                        <li><a href="#products">Spiral Tubes</a></li>
                        <li><a href="#products">Honeycomb Bubble Wrap</a></li>
                    </ul>
                </div>

                <div class="footer-col">
                    <h3>Contact</h3>
                    <ul>
                        <li><i class="fas fa-map-marker-alt"></i> SAFE CORRUGATED CONTAINERS PVT. LTD. <br> No. 12-15, Gopal Reddy Kandigai, Gummidipoondi,<br>  Chennai - 601 201, <br>  Tamil Nadu, India</li>
                        <li><i class="fas fa-phone"></i> +91 9894086315</li>
                        <li><i class="fas fa-envelope"></i>marketing.hexsa@gmail.com</li>
                    </ul>
                </div>
            </div>

            <div class="copyright">
                <p>&copy; 2023 Safe Corrugated Containers Pvt Ltd. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Mobile CTA -->
    <a href="tel:+912212345678" class="mobile-cta">
        <i class="fas fa-phone"></i>
    </a>

    <!-- JavaScript -->
    <script>
        // Mobile Navigation Toggle
        const mobileNavToggle = document.querySelector('.mobile-nav-toggle');
        const mobileNav = document.querySelector('.mobile-nav');

        mobileNavToggle.addEventListener('click', () => {
            const visibility = mobileNav.getAttribute('data-visible');
            
            if (visibility === "false") {
                mobileNav.setAttribute('data-visible', true);
                mobileNavToggle.setAttribute('aria-expanded', true);
                mobileNav.classList.add('active');
            } else {
                mobileNav.setAttribute('data-visible', false);
                mobileNavToggle.setAttribute('aria-expanded', false);
                mobileNav.classList.remove('active');
            }
        });

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });

                // Close mobile menu if open
                if (mobileNav.classList.contains('active')) {
                    mobileNav.setAttribute('data-visible', false);
                    mobileNavToggle.setAttribute('aria-expanded', false);
                    mobileNav.classList.remove('active');
                }
            });
        });

        // Sticky header on scroll
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            header.classList.toggle('sticky', window.scrollY > 0);
        });

        // Form submission handling
        document.getElementById('quoteForm').addEventListener('submit', function(e) {
            // Form validation
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.style.borderColor = 'red';
                    isValid = false;
                } else {
                    field.style.borderColor = '#ddd';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });

		const inquiryStatus = new URLSearchParams(window.location.search).get('inquiry');
		if (inquiryStatus === 'sent') {
			alert('Thank you. Your inquiry has been sent successfully.');
		} else if (inquiryStatus === 'invalid') {
			alert('Please check the required details and submit the form again.');
		} else if (inquiryStatus === 'rate-limited') {
			alert('Please wait a few seconds before sending another inquiry.');
		} else if (inquiryStatus === 'error') {
			alert('We could not send your inquiry right now. Please try again shortly.');
		}

		if (inquiryStatus) {
			window.history.replaceState({}, document.title, window.location.pathname + window.location.hash);
		}
		
		
		document.getElementById('contactForm').addEventListener('submit', function(e) 
		{
            let isValid = true;
            
         /*   // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.textContent = '');

            // Honeypot check (if filled, likely spam)
            if (document.getElementById('website').value) {
                alert('Spam detected!');
                isValid = false;
            }

            // Name validation
            if (!document.getElementById('name').value.trim()) {
                document.getElementById('nameError').textContent = 'Name is required';
                isValid = false;
            }

            // Email validation
            const email = document.getElementById('email').value;
            if (!email.includes('@') || !email.includes('.')) {
                document.getElementById('emailError').textContent = 'Invalid email';
                isValid = false;
            }
			
		/*	const phone = document.getElementById('phone').value;
			const cleanedPhone = phone.replace(/\D/g, '');
			if (phone.length < 10) 
			{
				document.getElementById('phoneError').textContent =  'Invalid phone number (must be at least 10 digits)';
				isValid = false;
			}

            if (!isValid) e.preventDefault(); // Stop form submission
        });
    </script>
</body>
</html>
