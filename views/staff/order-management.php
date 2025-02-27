<?php include '../layouts/header.php'; ?>

<div class="container mt-4">
    <h2>Manage Orders</h2>

    <?php if (isset($Orders) && !empty($Orders)) : ?>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($Orders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['id']) ?></td>
                    <td><?= htmlspecialchars($order['user_id']) ?></td>
                    <td>$<?= number_format($order['total_price'], 2) ?></td>
                    <td>
                        <form action="/staff/update-order/<?= $order['id'] ?>" method="POST" class="update-form">
                            <select name="status" class="form-select">
                                <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="Preparing" <?= $order['status'] == 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                                <option value="Ready" <?= $order['status'] == 'Ready' ? 'selected' : '' ?>>Ready</option>
                                <option value="Delivered" <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                            </select>
                    </td>
                    <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                    <td>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p class="alert alert-info">No orders found.</p>
    <?php endif; ?>

    <!-- Pagination Links -->
    <div class="pagination">
        <?php if (isset($page) && $page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="btn btn-sm btn-secondary">Previous</a>
        <?php endif; ?>
        
        <?php if (isset($page) && $page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="btn btn-sm btn-secondary">Next</a>
        <?php endif; ?>
    </div>
</div>

<?php include '../layouts/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

$(document).ready(function() {
        $('form.update-form').on('submit', function(e) {
    e.preventDefault();
    var form = $(this);
    var status = form.find('select[name="status"]').val();
    var orderId = form.attr('action').split('/').pop();
    $.ajax({
        url: form.attr('action'),
        method: 'POST',
        data: { status: status },
        success: function(response) {
            alert("Order status updated successfully.");
            form.closest('tr').find('td:nth-child(4)').text(status); // Update status cell
        },
        error: function() {
            alert("Error updating order status.");
        }
    });
});
});
</script>
