<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function purchase(Request $request)
    {
        return $this->render($request, 'Purchase', 'Buying animals, feed, and medicine.', [
            ['Purchase Orders', 'purchase-orders.index', 'Order and receive what you buy.'],
            ['Suppliers', 'suppliers.index', 'Who you buy from.'],
            ['Purchases Report', 'reports.purchases', 'Spending over time.', 'dashboard.view'],
        ]);
    }

    public function inventory(Request $request)
    {
        return $this->render($request, 'Inventory', 'Feed stock and what it is made of.', [
            ['Feed Items', 'feed-items.index', 'Stock on hand and restocking.'],
            ['Warehouses', 'warehouses.index', 'Where stock is kept.'],
            ['Ration Formulas', 'ration-formulas.index', 'Feed recipes per animal group.'],
            ['Stock Report', 'reports.stock', 'Stock levels and value.', 'dashboard.view'],
        ]);
    }

    public function sales(Request $request)
    {
        return $this->render($request, 'Sales', 'Selling animals and who to.', [
            ['New Sale', 'sales-orders.create', 'Record a sale to a customer.', 'salesorder.create'],
            ['Customers', 'customers.index', 'Who you sell to.'],
            ['Species Pricing', 'species.index', 'Default price per kg for each species.'],
            ['Sales Report', 'reports.sales', 'Revenue over time.', 'dashboard.view'],
        ]);
    }

    private function render(Request $request, string $title, string $blurb, array $links)
    {
        $links = array_values(array_filter(
            $links,
            fn (array $link) => ! isset($link[3]) || $request->user()->hasPermission($link[3]),
        ));

        return view('sections.show', compact('title', 'blurb', 'links'));
    }
}
