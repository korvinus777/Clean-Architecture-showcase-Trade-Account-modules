# Clean Architecture showcase: trading platform modules

Code excerpt from a production trading-platform backend (Laravel 11 + Octane, PHP 8.3).
It contains two modules, **Trade** (opening, editing and closing trades and pending orders)
and **Account** (trading accounts and balances), plus the HTTP layer that calls them.

> This is an excerpt published with the employer's permission. It does not run on its own:
> the shared kernel (`Platform\Package`), the other modules, and the infrastructure are left out.
> It is here so you can read how the layers and modules are structured.

## Structure

```
app/                       Laravel HTTP layer (thin)
  Http/Controllers/        one invokable controller per use case
  Http/Requests/           validation + domainRequest(): HTTP → domain Request DTO
routes/

modules/src/Modules/
  Trade/  Account/
    Domain/                business rules, framework-free
      Services/<UseCase>/  Input (port) · Request · Output (port) · Result · Service
      Entities/ ValueObjects/ Enums/ Dto/
      Repositories/        interfaces
      Adapters/            interfaces to other modules
    App/
      Outputs/<UseCase>/   presenters: implement Domain Output → JSON result
      Services/            application-level orchestration
    Api/                   the module's public facade for other modules (Api + DTOs)
    Infrastructure/
      Repositories/ Models/ Mappers/   Eloquent ↔ domain entities
      Adapters/<Module>/   implement Domain adapters by calling another module's Api
      Listeners/ Commands/ Providers/  (DI bindings for ports)
modules/depfile-layers.yaml    deptrac: dependency rules between layers
modules/depfile-modules.yaml   deptrac: isolation between modules
```

## Request flow: opening a trade

```
POST /trade
  → OpenTradeController                     (app/Http/Controllers/Trade)
      OpenTradeRequest::domainRequest()     validated HTTP input → Domain OpenTradeRequest
  → OpenTradeInput::open()                  Domain port, implemented by OpenTradeService
      AccountAdapter::findAccount()         Account module, reached through its Api
      SymbolAdapter / quote / lot rules     domain checks
      TradeRepository                       persistence behind an interface
      MetatraderAdapter / NativeTradeEngineAdapter   chosen by engine type
  → OpenTradeOutput::opened() | ::accountNotTradable() | ...
      OpenTradeJsonOutput                   App presenter → OpenTradeJsonResult
  → $result->response()                     JsonResponse
```

Entry point: `modules/src/Modules/Trade/Domain/Services/OpenTrade/OpenTradeService.php`.

## Design decisions

- **Input/Output ports per use case.** The service never builds an HTTP response. It reports
  each outcome through a method on the Output port (`symbolMarketClosed()`, `incorrectVolume()`, …).
  The presenter in `App/` turns that into a status code and an error code. The same use case can
  have other presenters, for example for console commands or engine events.
- **Dependencies point inward.** The rules in `depfile-layers.yaml` are:
  Domain → nothing; App → Domain; Api → App, Domain;
  Infrastructure → everything. Deptrac enforces this in CI.
- **Modules are isolated.** A module never imports another module's classes. It declares an
  adapter interface in its own Domain (`Trade\Domain\Adapters\AccountAdapter`). The
  implementation in `Infrastructure/Adapters` calls the other module's `Api`. These adapters are
  the only code excluded from the module deptrac rules.
- **Mappers.** Eloquent models stay in Infrastructure. Repositories return domain entities.
- **Strict typing.** `declare(strict_types=1)` and Psalm, with no `mixed`.
