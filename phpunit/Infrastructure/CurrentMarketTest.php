<?php
namespace Bga\Games\PyramidoCannonFodder\Infrastructure;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\Attributes;
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class CurrentMarketTest extends TestCase{
    protected ?CurrentMarket $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = CurrentMarket::create($this->mock_cards);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('market_provider')]
    public function test_get_market($retrieved_cards, $expected_dominoes) {
        // Arrange
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('quarry')->willReturn($retrieved_cards);

        // Act
        $dominoes = $this->sut->get_market();
        // Assert
        $this->assertEquals($expected_dominoes, $dominoes);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('market_provider')]
    public function test_get_next_market($retrieved_cards, $expected_dominoes) {
        // Arrange
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')->with('next')->willReturn($retrieved_cards);

        // Act
        $dominoes = $this->sut->get_next_market();
        // Assert
        $this->assertEquals($expected_dominoes, $dominoes);
    }
    static public function market_provider(): array {
        $retrieved_card1 = ['id' => 99, 'type' => 0, 'type_arg' => 0, 'location' => 'quarry', 'location_arg' => 1];
        $expected_domino1 = ['id' => 0, 'index' => 1, 'element_id' => 'quarry-1'];

        $retrieved_card5 = ['id' => 5, 'type' => 89, 'type_arg' => 5, 'location' => 'quarry', 'location_arg' => 3];
        $expected_domino5 = ['id' => 89, 'index' => 3, 'element_id' => 'quarry-3'];

        return [
            [[], []],
            [[$retrieved_card1], [1 => $expected_domino1]],
            [[$retrieved_card1, $retrieved_card5], [1 =>$expected_domino1, 3 => $expected_domino5]],
        ];
    }
}
?>
