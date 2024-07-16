
<?php
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                    $currentPage = basename($_SERVER['PHP_SELF']); // Mendapatkan halaman saat ini
                
                    switch ($page) {
                        case 'productmanagement':
                            if ($currentPage !== 'productmanagement.php') {
                                header("Location: productmanagement.php?page=productmanagement");
                                exit;
                            }
                            break;
                
                        case 'profile.php':
                            if ($currentPage !== 'profile.php.php') {
                                header("Location: profile.php.php?page=profile.php");
                                exit;
                            }
                            break;
                
                        case 'Classification':
                            if ($currentPage !== 'Classification.php') {
                                header("Location: Classification.php?page=Classification");
                                exit;
                            }
                            break;
                        
                            case 'supplier':
                            if ($currentPage !== 'supplier.php') {
                                header("Location: supplier.php?page=supplier");
                                exit;
                            }
                            break;
                        
                        case 'purchase_orders':
                            if ($currentPage !== 'purchase_orders.php') {
                                header("Location: purchase_orders.php?page=purchase_orders");
                                exit;
                            }
                            break;
                            
                        case 'dashboard':
                            if ($currentPage !== 'index.php') {
                                header("Location: index.php?page=dashboard");
                                exit;
                            }
                            break;
                
                        default:
                            // Handle cases for other pages or provide a default action
                            break;
                    }
                }
                ?>