<?php
namespace Bga\Games\PyramidoCannonFodder\UseCases;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/UseCases/ReturnAllMarkers.php');

include_once(__DIR__.'/../../export/modules/php/UseCases/GetAllDatas.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Marker.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

#[\AllowDynamicProperties]
class ReturnAllMarkersTest extends TestCase{
    protected ?ReturnAllMarkers $sut = null;
    protected ?FrameworkInterfaces\GameState $mock_gamestate = null;
    protected ?FrameworkInterfaces\Deck $mock_cards = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?GetAllDatas $mock_get_current_data = null;
    protected ?Infrastructure\UpdateMarker $mock_update_marker = null;

    protected int $player_id = 77;

    protected array $current_data = [];
    protected array $marker_specification = ['stage' => 4, 'horizontal' => 12, 'vertical' => 14, 'rotation' => 3, ];

    protected function setUp(): void {
        $this->mock_gamestate = $this->createMock(FrameworkInterfaces\GameState::class);
        $this->sut = ReturnAllMarkers::create($this->mock_gamestate);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->mock_get_current_data = $this->createMock(GetAllDatas::class);
        $this->sut->set_get_current_data($this->mock_get_current_data);

        $this->mock_update_marker = $this->createMock(Infrastructure\UpdateMarker::class);
        $this->sut->set_update_marker($this->mock_update_marker);
    }

    public function test_return_markers() {
        // Arrange
        $markers = [$this->player_id => ['id' => 3]];
        $this->mock_get_current_data->expects($this->exactly(2))->method('get')->willReturn(
            ['markers' => $markers]);
        $this->mock_update_marker->expects($this->exactly(1))->method('return_all_markers')->with($this->player_id);
        $this->mock_notifications->expects($this->exactly(1))->method('notifyAllPlayers')
        ->with('return_all_markers', '', ['markers' => $markers]);

        // Act
        $this->sut->execute();
        // Assert
    }

    public function test_stage_1_filled() {
        // Arrange
        $tiles = $this->create_tiles([20]);
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn(['tiles'=> [77 =>$tiles]]);

        // Act
        $transition_name = $this->sut->get_transition_name();
        // Assert
        $this->assertEquals('not_finished_playing', $transition_name);
    }

    public function test_stage_4_filled() {
        // Arrange
        $tiles = $this->create_tiles([20, 6, 3, 2]);
        $this->mock_get_current_data->expects($this->exactly(1))->method('get')->willReturn(
            ['tiles'=> [77 =>$tiles]]);

        // Act
        $transition_name = $this->sut->get_transition_name();
        // Assert
        $this->assertEquals('finished_playing', $transition_name);
    }

    protected function create_tiles($number_tiles_per_stage): array {
        $tiles = [];
        $stage_number = 0;
        foreach ($number_tiles_per_stage as $number_tiles) {
            $stage_number = $stage_number + 1;
            for ($i=0; $i < $number_tiles; $i++)
                $tiles[] = ['stage' => $stage_number];
        }
        return $tiles;
    }
}

?>
