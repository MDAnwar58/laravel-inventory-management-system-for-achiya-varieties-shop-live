<style>
    .product-image {
      max-width: 100%;
      height: 300px;
      object-fit: cover;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .info-card {
      background: white;
      border-radius: 15px;
      padding: 2rem;
      margin-bottom: 1.5rem;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      border: none;
    }

    .status-badge {
      font-size: 0.85rem;
      padding: 0.5rem 1rem;
      border-radius: 25px;
    }

    .price-tag {
      font-size: 2rem;
      font-weight: bold;
      color: #27ae60;
    }

    .discount-badge {
      background: linear-gradient(45deg, #e74c3c, #c0392b);
      color: white;
      padding: 0.3rem 0.8rem;
      border-radius: 15px;
      font-size: 0.8rem;
      margin-left: 1rem;
    }

    .nav-pills .nav-link.active {
      background: linear-gradient(45deg, #3498db, #2980b9);
    }

    .action-buttons .btn {
      margin: 0.25rem;
      border-radius: 8px;
      padding: 0.6rem 1.2rem;
    }

    .gallery-thumbnail {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      border: 2px solid transparent;
    }

    .gallery-thumbnail:hover,
    .gallery-thumbnail.active {
      border-color: #3498db;
      transform: scale(1.05);
    }

    .specs-table th {
      background: #f8f9fa;
      font-weight: 600;
      width: 30%;
    }
  </style>