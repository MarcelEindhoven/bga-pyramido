<?php
namespace Bga\Games\Pyramido\Infrastructure;
/**
 *------
 * Pyramido implementation unit tests : © Marcel van Nieuwenhoven marcel.eindhoven@hotmail.com
 *
 */

include_once(__DIR__.'/../../vendor/autoload.php');
use PHPUnit\Framework\TestCase;

include_once(__DIR__.'/../../export/modules/php/BGA/Database.php');
include_once(__DIR__.'/../../export/modules/php/BGA/Score.php');

include_once(__DIR__.'/../_ide_helper.php');
use Bga\Games\FrameworkInterfaces;

class ScoreTest extends TestCase{
    protected ?\NieuwenhovenGames\BGA\Score $sut = null;
    protected ?FrameworkInterfaces\Table $mock_notifications = null;
    protected ?\NieuwenhovenGames\BGA\Database $mock_database = null;

    protected string $player_id = '77';
    protected array $players = ['77' => [],];

    protected function setUp(): void {
        $this->mock_database = $this->createMock(\NieuwenhovenGames\BGA\Database::class);
        $this->sut = new \NieuwenhovenGames\BGA\Score($this->mock_database);

        $this->mock_notifications = $this->createMock(FrameworkInterfaces\Table::class);
        $this->sut->set_notifications($this->mock_notifications);

        $this->sut->set_players($this->players);
    }

    public function test_category() {
        // Arrange
        $this->mock_database->expects($this->exactly(1))->method('query')
        ->with("UPDATE `player` SET `player_score` = `player_score` + 5 WHERE `player_id` = '".$this->player_id."'");

        // Act
        $this->sut->add($this->player_id, 5);
        // Assert
    }

}
?>
