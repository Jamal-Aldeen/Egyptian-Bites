<?php
require_once __DIR__ . '/../models/Inventory.php';
require_once __DIR__ . '/../controllers/MenuController.php';

class NotificationController {
    private $inventoryModel;
    private $menuController;

    public function __construct() {
        $this->inventoryModel = new Inventory();
        $this->menuController = new MenuController();
    }

    // Get low stock notifications
    public function getLowStockNotifications() {
        return $this->inventoryModel->getLowStockItems();
    }

    // Get expiring special offers
    public function getExpiringOfferNotifications() {
        return $this->menuController->getExpiringOffers();
    }
}
?>