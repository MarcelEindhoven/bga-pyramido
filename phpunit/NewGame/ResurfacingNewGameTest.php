<?php
namespace Bga\Games\PyramidoCannonFodder\NewGame;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/NewGame/ResurfacingNewGame.php');

include_once(__DIR__.'/../../export/modules/php/Infrastructure/Resurfacing.php');
use Bga\Games\PyramidoCannonFodder\Infrastructure;

class ResurfacingNewGameTest extends TestCase{
    protected ?ResurfacingNewGame $sut = null;
    protected ?Infrastructure\ResurfacingFactory $mock_resurfacing_factory = null;
    protected array $players = ['7' => [], '77' => [],];

    public function setup(): void {
        $this->mock_resurfacing_factory = $this->createMock(Infrastructure\ResurfacingFactory::class);
        $this->sut = new ResurfacingNewGame();
        $this->sut->set_resurfacing_factory($this->mock_resurfacing_factory);

        $this->sut->set_players($this->players);
    }

    public function test_resurfacing_factory_add() {
        // Arrange
        $this->mock_resurfacing_factory->expects($this->exactly(ResurfacingNewGame::SIZE * count($this->players)))->method('add');
        // Act
        $this->sut->setup();
        // Assert
    }

    public function test_resurfacing_factory_flush() {
        // Arrange
        $this->mock_resurfacing_factory->expects($this->exactly(count($this->players)))->method('flush');
        // Act
        $this->sut->setup();
        // Assert
    }
}
?>
