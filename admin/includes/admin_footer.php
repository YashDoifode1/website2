            </main>
            
            <!-- Footer -->
            <footer class="admin-footer">
                <p>&copy; <?= date('Y') ?> Rakhi Construction & Consultancy Pvt Ltd Admin Panel. All rights reserved.</p>
            </footer>
        </div>
    </div>
    
    <script>
        // Initialize Feather Icons
        feather.replace();
        
        // Confirm delete actions
        document.querySelectorAll('a[href*="action=delete"], .btn-delete').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
