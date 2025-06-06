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
    protected array $default_domino = ['id' => 0, 'type' => 0, 'type_arg' => 0, 'location' => '0', 'location_arg' => 0];

    protected function setUp(): void {
        $this->mock_dominoes = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = CurrentTiles::create($this->mock_dominoes);
        $this->sut->set_players($this->players);
    }

    public function test_get_domino() {
        // Arrange
        $stage = 2;
        $horizontal = 11;
        $vertical = 5;
        $rotation = 3;
        $domino_specification = ['stage' => $stage, 'horizontal' => $horizontal, 'vertical' => $vertical, 'rotation' => $rotation, ];
        $this->mock_dominoes->expects($this->exactly(1))->method('getCardsInLocation')->with('77'
        , $stage
        + $horizontal * CurrentTiles::FACTOR_STAGE
         + $vertical * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL
         + $rotation * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL * CurrentTiles::FACTOR_VERTICAL)->willReturn([$this->default_domino]);

        // Act
        $domino = $this->sut->get_domino($this->player_id, $domino_specification);
        // Assert
        $this->assertEquals($this->default_domino, $domino);
    }

    public function test_get_dominoes() {
        // Arrange
        $stage = 3;
        $this->default_domino['location_arg'] = $stage + 13 * CurrentTiles::FACTOR_STAGE;
        $this->mock_dominoes->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn([$this->default_domino]);

        // Act
        $dominoes = $this->sut->get_dominoes($this->player_id);
        // Assert
        $this->assertEquals($stage, end($dominoes)['stage']);
    }

    public function test_get_double_tile_per_domino() {
        // Arrange
        $stage = 3;
        $this->default_domino['location_arg'] = $stage;
        
        $this->arrange_default_domino();

        // Act
        $tiles = $this->act_default($stage);
        // Assert
        $this->assertEquals(2, sizeof($tiles));
    }

    public function test_category() {
        // Arrange
        $this->arrange_default_domino();

        // Act
        $first_tile = $this->act_first_tile();
        // Assert
        $this->assertEquals('tile', $first_tile['class']);
    }

    public function test_horizontal_first_tile() {
        // Arrange
        $stage = 3;
        $horizontal = 10;
        $this->default_domino['location_arg'] = $stage + $horizontal * CurrentTiles::FACTOR_STAGE;
        
        $this->arrange_default_domino();

        // Act
        $first_tile = $this->act_first_tile();
        // Assert
        $this->assertEquals($horizontal, $first_tile['horizontal']);
    }

    public function test_vertical_first_tile() {
        // Arrange
        $stage = 3;
        $horizontal = 19;
        $vertical = 10;
        $this->default_domino['location_arg'] = $stage
         + $horizontal * CurrentTiles::FACTOR_STAGE
          + $vertical * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL;
        
        $this->arrange_default_domino();

        // Act
        $first_tile = $this->act_first_tile();
        // Assert
        $this->assertEquals($horizontal, $first_tile['horizontal']);
        $this->assertEquals($vertical, $first_tile['vertical']);
    }

    public function test_rotation_first_tile() {
        // Arrange
        $stage = 3;
        $horizontal = 0;
        $vertical = 19;
        $rotation = 1;
        $this->default_domino['location_arg'] = $stage
         + $horizontal * CurrentTiles::FACTOR_STAGE
          + $vertical * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL
          + $rotation * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL * CurrentTiles::FACTOR_VERTICAL;
        
        $this->arrange_default_domino();

        // Act
        $first_tile = $this->act_first_tile();
        // Assert
        $this->assertEquals($horizontal, $first_tile['horizontal']);
        $this->assertEquals($vertical, $first_tile['vertical']);
        $this->assertEquals($rotation, $first_tile['rotation']);
    }

    public function test_rotation_second_tile() {
        // Arrange
        $stage = 3;
        $horizontal = 0;
        $vertical = 19;
        $rotation = 3;
        $this->default_domino['location_arg'] = $stage
         + $horizontal * CurrentTiles::FACTOR_STAGE
          + $vertical * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL
          + $rotation * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL * CurrentTiles::FACTOR_VERTICAL;
        
        $this->arrange_default_domino();

        // Act
        $second_tile = $this->act_second_tile();
        // Assert
        $this->assertEquals($rotation, $second_tile['rotation']);
    }

    public function test_second_tile_rotation0() {
        // Arrange
        $this->arrange_rotation(0);

        // Act
        $second_tile = $this->act_second_tile();

        // Assert
        $this->assertEquals(10 + 2, $second_tile['horizontal']);
        $this->assertEquals(12 + 0, $second_tile['vertical']);
    }

    public function test_second_tile_rotation1() {
        // Arrange
        $this->arrange_rotation(1);

        // Act
        $second_tile = $this->act_second_tile();

        // Assert
        $this->assertEquals(10 + 0, $second_tile['horizontal']);
        $this->assertEquals(12 + 2, $second_tile['vertical']);
    }

    public function test_second_tile_rotation2() {
        // Arrange
        $this->arrange_rotation(2);

        // Act
        $second_tile = $this->act_second_tile();

        // Assert
        $this->assertEquals(10 - 2, $second_tile['horizontal']);
        $this->assertEquals(12 + 0, $second_tile['vertical']);
    }

    public function test_second_tile_rotation3() {
        // Arrange
        $this->arrange_rotation(3);

        // Act
        $second_tile = $this->act_second_tile();

        // Assert
        $this->assertEquals(10 + 0, $second_tile['horizontal']);
        $this->assertEquals(12 - 2, $second_tile['vertical']);
    }

    public function test_second_tile_tile_id() {
        // Arrange
        $this->default_domino['type'] = 1;
        $this->arrange_rotation(3);

        // Act
        $second_tile = $this->act_second_tile();

        // Assert
        $this->assertEquals($this->default_domino['type'] * 2 + 1, $second_tile['tile_id']);
    }

    public function test_tile_id_first_tile() {
        // Arrange
        $stage = 1;
        $this->default_domino['location_arg'] = $stage;

        $this->default_domino['type'] = 90;
        
        $this->arrange_default_domino();

        // Act
        $first_tile = $this->act_first_tile();

        // Assert
        $this->assertEquals($this->default_domino['type'] * 2, $first_tile['tile_id']);
    }

    public function test_first_tile_colour() {
        // Arrange
        $colour = 5;
        $this->default_domino['type_arg'] = $colour + 6 * 5;

        $this->arrange_default_domino();

        // Act
        $first_tile = $this->act_first_tile();

        // Assert
        $this->assertEquals($colour, $first_tile['colour']);
    }

    public function test_second_tile_colour() {
        // Arrange
        $colour = 3;
        $this->default_domino['type_arg'] = 5 + 6 * $colour;
        $this->arrange_rotation(3);

        // Act
        $second_tile = $this->act_second_tile();

        // Assert
        $this->assertEquals($colour, $second_tile['colour']);
    }

    public function test_first_tile_no_jewels() {
        // Arrange
        $this->default_domino['type_arg'] = 5 + 6 * 5 + 6*6 * 4 + 6*6*8 * 7;

        $this->arrange_default_domino();

        // Act
        $first_tile = $this->act_first_tile();

        // Assert
        $this->assertEquals([], $first_tile['jewels']);
    }

    public function test_second_tile_no_jewels() {
        // Arrange
        $this->default_domino['type_arg'] = 5 + 6 * 5;

        $this->arrange_default_domino();

        // Act
        $second_tile = $this->act_second_tile();

        // Assert
        $this->assertEquals([], $second_tile['jewels']);
    }

    public function test_first_tile_single_jewel() {
        // Arrange
        $jewels = [3];
        $this->default_domino['type_arg'] = 5 + 6 * 5 + 6*6 * $jewels[0] + 6*6*8 * 7;

        $this->arrange_default_domino();

        // Act
        $first_tile = $this->act_first_tile();

        // Assert
        $this->assertEquals($jewels, $first_tile['jewels']);
    }

    public function test_first_tile_double_jewels() {
        // Arrange
        $jewels = [0, 3];
        $this->default_domino['type_arg'] = 5 + 6 * 5 + 6*6 * $jewels[0] + 6*6*8 * $jewels[1];

        $this->arrange_default_domino();

        // Act
        $first_tile = $this->act_first_tile();

        // Assert
        $this->assertEquals($jewels, $first_tile['jewels']);
    }

    public function test_second_tile_single_jewel() {
        // Arrange
        $jewels = [0];
        $this->default_domino['type_arg'] = 5 + 6 * 5 + 6*6*8 * ($jewels[0] + 4);

        $this->arrange_default_domino();

        // Act
        $second_tile = $this->act_second_tile();

        // Assert
        $this->assertEquals($jewels, $second_tile['jewels']);
    }

    public function test_second_tile_double_jewels() {
        // Arrange
        $jewels = [0, 3];
        $this->default_domino['type_arg'] = 5 + 6 * 5 + 6*6 * ($jewels[0] + 4) + 6*6*8 * ($jewels[1] + 4);

        $this->arrange_default_domino();

        // Act
        $second_tile = $this->act_second_tile();

        // Assert
        $this->assertEquals($jewels, $second_tile['jewels']);
    }

    protected function arrange_default_domino() {
        $this->mock_dominoes->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn([$this->default_domino]);
    }

    protected function arrange_rotation($rotation) {
        $stage = 3;
        $horizontal = 10;
        $vertical = 12;
        $this->default_domino['location_arg'] = $stage
         + $horizontal * CurrentTiles::FACTOR_STAGE
          + $vertical * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL
          + $rotation * CurrentTiles::FACTOR_STAGE * CurrentTiles::FACTOR_HORIZONTAL * CurrentTiles::FACTOR_VERTICAL;
        $this->mock_dominoes->expects($this->exactly(1))->method('getCardsInLocation')->with('77')->willReturn([$this->default_domino]);
    }
    protected function calculate_array_index_second_tile($domino) {
        return $this->sut->calculate_array_index($this->sut->get_second_tile_for($domino));
    }
    protected function calculate_array_index_first_tile($domino) {
        return $this->sut->calculate_array_index($this->sut->get_first_tile_for($domino));
    }

    protected function act_second_tile() {
        return $this->sut->get()[$this->player_id][$this->calculate_array_index_second_tile($this->default_domino)];
    }

    protected function act_first_tile() {
        return $this->sut->get()[$this->player_id][$this->calculate_array_index_first_tile($this->default_domino)];
    }

    protected function act_default() {
        return $this->sut->get()[$this->player_id];
    }
}
?>
