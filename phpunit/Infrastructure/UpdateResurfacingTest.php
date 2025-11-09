<?php
namespace Bga\Games\Pyramido\Infrastructure;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Resurfacing.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class UpdateResurfacingTest extends TestCase{
    protected ?UpdateResurfacing $sut = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;

    protected int $player_id = 77;
    protected int $stage = 4;
    protected int $horizontal = 19;
    protected int $vertical = 18;
    protected int $rotation = 3;
    protected array $resurfacing_specification = ['id' => 99, 'stage' => 2, 'horizontal' => 19, 'vertical' => 18, 'rotation' => 3, 'colour' => 3, ];
    protected array $resurfacing_card = ['id' => 99, 'type' => 2, 'type_arg' => 3, 'location' => 77, 'location_arg' => 0,];

    protected function setUp(): void {
        $this->mock_cards = $this->createMock(FrameworkInterfaces\Deck::class);
        $this->sut = UpdateResurfacing::create($this->mock_cards);
    }

    public function test_colour_even() {
        // Arrange
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')
        ->with(strval($this->player_id), 0)->willReturn([$this->resurfacing_card]);

        $this->resurfacing_specification['colour'] = $this->resurfacing_card['type'];
        $this->mock_cards->expects($this->exactly(1))->method('moveCard')
        ->with(99, $this->player_id
        , $this->resurfacing_specification['stage'] + 5 * $this->resurfacing_specification['horizontal'] + 5*20* $this->resurfacing_specification['vertical'] + 5*20*20* $this->resurfacing_specification['rotation'] + 5*20*20*4* ($this->resurfacing_specification['colour'] % 2));

        // Act
        $this->sut->move_to_pyramid($this->player_id, $this->resurfacing_specification);
        // Assert
    }

    public function test_colour_odd() {
        // Arrange
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')
        ->with(strval($this->player_id), 0)->willReturn([$this->resurfacing_card]);

        $this->resurfacing_specification['colour'] = $this->resurfacing_card['type_arg'];
        $this->mock_cards->expects($this->exactly(1))->method('moveCard')
        ->with(99, $this->player_id
        , $this->resurfacing_specification['stage'] + 5 * $this->resurfacing_specification['horizontal'] + 5*20* $this->resurfacing_specification['vertical'] + 5*20*20* $this->resurfacing_specification['rotation'] + 5*20*20*4* ($this->resurfacing_specification['colour'] % 2));

        // Act
        $this->sut->move_to_pyramid($this->player_id, $this->resurfacing_specification);
        // Assert
    }

    public function test_get_both_unplaced() {
        // Arrange
        $this->resurfacing_specification['colour'] = $this->resurfacing_card['type_arg'];
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')
        ->with(strval($this->player_id), 0)->willReturn([$this->resurfacing_card]);

        // Act
        $unplaced_tiles = $this->sut->get_both_unplaced($this->player_id, $this->resurfacing_specification);
        // Assert
        $this->assertEquals(2, count($unplaced_tiles));
    }

    public function test_get_no_unplaced() {
        // Arrange
        $this->resurfacing_specification['colour'] = 7;
        $this->mock_cards->expects($this->exactly(1))->method('getCardsInLocation')
        ->with(strval($this->player_id), 0)->willReturn([$this->resurfacing_card]);

        // Act
        $unplaced_tiles = $this->sut->get_both_unplaced($this->player_id, $this->resurfacing_specification);
        // Assert
        $this->assertEqualsCanonicalizing([], $unplaced_tiles);
    }
}
?>
