<?php


class Basket
{
    private array $product_catalogue = [];
    private array $delivery_charge_rules = [];
    private array $special_offers = [];
    private array $allowed_codes = [];
    private array $codes_with_special_offer = [];
    private array $products_with_applied_special_offers = [];
    private array $products = [];

    /**
     * @param array $product_catalogue
     * @param array $delivery_charge_rules
     * @param array $special_offers
     */
    public function __construct(array $product_catalogue, array $delivery_charge_rules, array $special_offers)
    {
        $this->product_catalogue = $this->normalizeProductCatalogue($product_catalogue);
        $this->delivery_charge_rules = $delivery_charge_rules;
        $this->special_offers = $special_offers;
        $this->allowed_codes = array_keys($this->product_catalogue);
        $this->codes_with_special_offer = $this->getCodesWithSpecialOffer();
    }

    /**
     * @param array $product_catalogue
     * @return array
     */
    private function normalizeProductCatalogue(array $product_catalogue): array
    {
        $normalized_product_catalogue = [];
        foreach ($product_catalogue as $i => $product) {
            $normalized_product_catalogue[$product['code']] = $product['price'];
        }

        return $normalized_product_catalogue;
    }

    /**
     * @return array
     */
    private function getCodesWithSpecialOffer(): array
    {
        $codes_with_special_offer = [];
        foreach ($this->special_offers as $i => $offer) {
            if (!in_array($offer['product_code'], $codes_with_special_offer)) {
                $codes_with_special_offer[] = $offer['product_code'];
            }
        }

        return $codes_with_special_offer;
    }

    /**
     * @param float $total_price
     * @return float
     * @throws Exception
     */
    private function getDeliveryPrice(float $total_price): float
    {
        foreach ($this->delivery_charge_rules as $i => $rule) {
            if ($total_price >= $rule['min_total_price'] && $total_price <= $rule['max_total_price']) {
                return $rule['delivery_cost'];
            }
        }

        throw new Exception('Delivery charge rules are set incorrectly.');
    }


    /**
     * @param string $product_code
     * @return float
     */
    private function getProductPrice(string $product_code): float
    {
        $price = $this->product_catalogue[$product_code];

        return $this->applySpecialOffer($price, $product_code);
    }

    /**
     * @param float $price
     * @param string $product_code
     * @return float
     */
    private function applySpecialOffer(float $price, string $product_code): float
    {
        $special_offer_multiplier = 1;
        foreach ($this->special_offers as $i => $offer) {
            if (
                $offer['product_code'] === $product_code &&
                !in_array($product_code, $this->products_with_applied_special_offers) &&
                array_count_values($this->products)[$product_code] >= $offer['min_quantity']
            ) {
                $special_offer_multiplier = $offer['discount_multiplier'];
                $this->products_with_applied_special_offers[] = $product_code;
                break;
            }
        }

        return $price * $special_offer_multiplier;
    }

    /**
     * @param string $code
     * @throws Exception
     */
    public function add(string $code): void
    {
        if (!in_array($code, $this->allowed_codes)) {
            throw new Exception('You entered invalid product code, please try again.');
        }
        $this->products[] = $code;
    }

    /**
     * @return float
     */
    public function total(): float
    {
        $total = 0;
        foreach ($this->products as $product_code) {
            $total += $this->getProductPrice($product_code);
        }
        try {
            $total += $this->getDeliveryPrice($total);

        } catch (Exception $exception) {
            echo $exception->getMessage() . "\n";
            exit;
        }

        return floor($total * pow(10, 2)) / pow(10, 2);
    }
}