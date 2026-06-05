<!-- Footer -->
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // Auto-hide alert after 3 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
</script>

<?php if (isset($_SESSION['success'])): ?>
    <script>
        alert('<?= $_SESSION['success'] ?>');
        <?php unset($_SESSION['success']); ?>
    </script>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <script>
        alert('<?= $_SESSION['error'] ?>');
        <?php unset($_SESSION['error']); ?>
    </script>
<?php endif; ?>
</body>

</html>