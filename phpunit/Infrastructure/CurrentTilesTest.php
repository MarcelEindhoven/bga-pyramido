<?php
namespace Bga\Games\PyramidoCannonFodder\Infrastructure;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Domino.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class CurrentTilesTest extends TestCase{
    protected ?CurrentTiles $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_dominoes = null;
    protected array $players = ['77' => [],];

    protected function setUp(): void {
        $this->mock_dominoes = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = CurrentTiles::create($this->mock_dominoes);
        $this->sut->set_players($this->players);
    }

    /**
     * @dataProvider market_provider
     */
    public function test_get_market($retrieved_cards, $expected_tiles) {
        // Arrange
        $this->mock_dominoes->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn($retrieved_cards);

        // Act
        $tiles = $this->sut->get();
        // Assert
        $this->assertEqualsCanonicalizing($expected_tiles, $tiles);
    }
    static public function market_provider(): array {
        $retrieved_card1 = ['id' => 1, 'type' => 0, 'type_arg' => 0, 'location' => 'market', 'location_arg' => 0];
        $expected_domino1 = ['id' => 1, 'tiles' => [['colour' => 0], ['colour' => 0]]];

        $retrieved_card5 = ['id' => 5, 'type' => 1, 'type_arg' => 5, 'location' => 'market', 'location_arg' => 0];
        $expected_domino5 = ['id' => 5, 'tiles' => [['colour' => 1], ['colour' => 5]]];

        return [
            [[], []],
        ];
    }
}
?>
