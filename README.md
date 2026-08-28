# Pocketmine-MP-SignUI

**API**

use gamegam\SignUI\FakeBlock\SignBlock;

SignBlock::getInstance()->SignCreate(Player, 고유번호); // 표지판 생성 함수

public function SignInputEvent(SignInputEvent $ev){
  $p = $ev->getPlayer(); // 플레이어 객체
  $line = $ev->getSign()[0] ?? null; // 표지판 0라인]
  $type = $ev->getType(); // 표지판 고유 생성 함수
  if ($type === "고유번호"){
     $p->sendMessage($line . "을 작성하셨습니다.");
  }
}
