<?php


class Interactions
{
    private Basket $basket;

    /**
     * Interactions constructor.
     * @param Basket $basket
     */
    public function __construct(Basket $basket)
    {
        $this->basket = $basket;
    }

    /**
     * @return string
     */
    private function getUserResponse(): string
    {
        return rtrim(fgets(STDIN));
    }

    /**
     * @param string $response
     */
    private function handleUserResponse(string $response): void
    {
        if ($response === 'total') {
            $total = $this->basket->total();
            echo "Total cost is: $" . $total . "\n";
            exit;
        }
        try {
            $this->basket->add($response);
            echo "Product " . $response . " added to the basket.\n";
        } catch (Exception $exception) {
            echo $exception->getMessage() . "\n";
        }

        $this->interact();
    }

    public function interact(): void
    {
        echo "Enter a code to add product to a basket or enter total to calculate a total cost: ";
        $response = $this->getUserResponse();
        $this->handleUserResponse($response);
    }
}