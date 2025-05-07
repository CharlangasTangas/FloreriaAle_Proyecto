<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#3b82f6',
                            foreground: '#ffffff',
                        },
                        sidebar: {
                            DEFAULT: '#f8fafc',
                            foreground: '#475569',
                            border: '#e2e8f0',
                            accent: '#f1f5f9',
                            'accent-foreground': '#1e293b',
                        },
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Custom styles */
        .sidebar-collapsed {
            width: 4rem;
        }
        .sidebar-expanded {
            width: 16rem;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -16rem;
                z-index: 50;
                transition: left 0.3s ease;
            }
            .sidebar.open {
                left: 0;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">