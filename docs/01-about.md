# About

`phpsoftbox/events` — компонент событий и слушателей, совместимый с PSR-14.
Поддерживает регистрацию слушателей, атрибуты для описания связей и
отложенные события через очередь.

Основные элементы:
- `EventDispatcher` — диспатчер и реестр слушателей
- `ListenTo` — атрибут для описания слушателей
- `EventLoader` — загрузчик конфигураций из `config/events`
- `QueueableEventInterface` — интерфейс для отложенных событий
- `EventQueueJobHandler` — обработчик очереди для событий
- `BroadcastableEventInterface` — интерфейс для событий в сокет (broadcast)
