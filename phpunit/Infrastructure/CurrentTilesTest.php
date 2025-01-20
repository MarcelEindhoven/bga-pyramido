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
    protected string $player_id = '77';
    protected array $players = ['77' => [],];
    protected array $default_tile = ['id' => 0, 'type' => 0, 'type_arg' => 0, 'location' => '0', 'location_arg' => 0];

    protected function setUp(): void {
        $this->mock_dominoes = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = CurrentTiles::create($this->mock_dominoes);
        $this->sut->set_players($this->players);
    }

    /**
     * @dataProvider tile_provider
     */
    public function test_get_tile($retrieved_cards, $expected_tiles) {
        // Arrange
        $this->mock_dominoes->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn($retrieved_cards);

        // Act
        $tiles = $this->sut->get();
        // Assert
        $this->assertEqualsCanonicalizing($expected_tiles, $tiles[$this->player_id]);
    }
    static public function tile_provider(): array {
        $retrieved_card_rotation0 = ['id' => 1, 'type' => 0, 'type_arg' => 0, 'location' => '77', 'location_arg' => 1051];
        $expected_tile_rotation0_first = ['id' => '1', 'colour' => 0, 'stage' => 1, 'horizontal' => 10, 'vertical' => 10, 'rotation' => 0];
        $expected_tile_rotation0_second = ['id' => '1', 'colour' => 0, 'stage' => 1, 'horizontal' => 11, 'vertical' => 10, 'rotation' => 0];

        $retrieved_card_rotation1 = ['id' => 1, 'type' => 0, 'type_arg' => 0, 'location' => '77', 'location_arg' => 3859];
        $expected_tile_rotation1_first = ['id' => '1', 'colour' => 0, 'stage' => 4, 'horizontal' => 11, 'vertical' => 18, 'rotation' => 1];
        $expected_tile_rotation1_second = ['id' => '1', 'colour' => 0, 'stage' => 4, 'horizontal' => 11, 'vertical' => 19, 'rotation' => 1];

        return [
            [[], []],
            [[$retrieved_card_rotation0], [1 => [$expected_tile_rotation0_first, $expected_tile_rotation0_second]]],
            [[$retrieved_card_rotation1], [1 => [$expected_tile_rotation1_first, $expected_tile_rotation1_second]]],
        ];
    }
    static public function random_provider(): array {
        return [
            [[], []],
            [[$retrieved_card_rotation0], [1 => [$expected_tile_rotation0_first, $expected_tile_rotation0_second]]],
            [[$retrieved_card_rotation1], [1 => [$expected_tile_rotation1_first, $expected_tile_rotation1_second]]],
        ];
    }

}
?>
