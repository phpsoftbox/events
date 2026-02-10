# CLI

Команды для просмотра зарегистрированных событий:

```
event:list
```

Пример:

```bash
php psb event:list
```

## Генерация событий и слушателей

Создать класс события:

```bash
php psb make:event App\\Events\\UserRegistered
```

Создать слушатель с обработчиком:

```bash
php psb make:listener App\\Listeners\\SendWelcomeEmail
```

При указании `--event` генератор добавит атрибут `ListenTo` и тип аргумента:

```bash
php psb make:listener App\\Listeners\\AuditListener --event=App\\Events\\UserRegistered
```

Если передать путь, он будет использован напрямую:

```bash
php psb make:event src/Events/UserRegistered.php
```

По умолчанию используется `--namespace=App` и `--path=src`, их можно переопределить:

```bash
php psb make:listener App\\Listeners\\AuditListener --event=App\\Events\\UserRegistered --path=src --namespace=App
```
