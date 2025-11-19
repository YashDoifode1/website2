<?php
/**
 * Error Page Header
 */
require_once __DIR__ . '/../config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($error_title) ? $error_title . ' - ' : '' ?><?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-yellow: #F9A826;
            --charcoal: #1A1A1A;
            --white: #fff;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--charcoal);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        .error-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }
        
        .error-content {
            max-width: 600px;
            padding: 2.5rem;
            background: var(--white);
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
            position: relative;
            overflow: hidden;
        }
        
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            color: var(--primary-yellow);
            line-height: 1;
            margin-bottom: 1rem;
        }
        
        .error-title {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--charcoal);
        }
        
        .error-message {
            color: #6c757d;
            margin-bottom: 2rem;
            font-size: 1.1rem;
        }
        
        .btn-home {
            background-color: var(--primary-yellow);
            color: var(--charcoal);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s ease;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 0.5px;
        }
        
        .btn-home:hover {
            background-color: #e69500;
            color: var(--charcoal);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(249, 168, 38, 0.3);
        }
        
        .error-illustration {
            max-width: 300px;
            margin: 0 auto 2rem;
        }
        
        @media (max-width: 576px) {
            .error-code {
                font-size: 6rem;
            }
            
            .error-title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <div class="error-illustration">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" fill="currentColor">
                    <path d="M256 8C119 8 8 119 8 256s111 248 248 248 248-111 248-248S393 8 256 8zm0 448c-110.5 0-200-89.5-200-200S145.5 56 256 56s200 89.5 200 200-89.5 200-200 200zm-11.5-104.5l7.5-60.5h-40l7.5 60.5c0 4.1 3.7 7.5 8.5 7.5h8c4.8 0 8.5-3.4 8.5-7.5zM256 128c-26.5 0-48 21.5-48 48s21.5 48 48 48 48-21.5 48-48-21.5-48-48-48z"/>
                </svg>
            </div>
