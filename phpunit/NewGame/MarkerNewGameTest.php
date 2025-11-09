<?php
namespace Bga\Games\Pyramido\NewGame;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/NewGame/MarkerNewGame.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Marker.php');
use Bga\Games\Pyramido\Infrastructure;

class MarkerNewGameTest extends TestCase{
    protected ?MarkerNewGame $sut = null;
    protected ?Infrastructure\MarkerFactory $mock_marker_factory = null;
    protected array $players = ['7' => [], '77' => [],];

    public function setup(): void {
        $this->mock_marker_factory = $this->createMock(Infrastructure\MarkerFactory::class);
        $this->sut = new MarkerNewGame();
        $this->sut->set_marker_factory($this->mock_marker_factory);

        $this->sut->set_players($this->players);

    }

    /**
     */
    public function test_marker_factory_add() {
        // Arrange
        $this->mock_marker_factory->expects($this->exactly(MarkerNewGame::SIZE * count($this->players)))->method('add');
        // Act
        $this->sut->setup();
        // Assert
    }

    public function test_marker_factory_flush() {
        // Arrange
        $this->mock_marker_factory->expects($this->exactly(count($this->players)))->method('flush');
        // Act
        $this->sut->setup();
        // Assert
    }
}
?>
