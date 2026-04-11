## Vertical Slice + CQRS

Chaque use case vit dans son propre dossier (vertical slice), par exemple:

- `Auth/Login`
- `Passwords/CreatePassword`

Dans chaque slice:

- `*Command` ou `*Query`
- `*Handler`
- `*Controller` / `*Request` / `*Result` selon le besoin

Les handlers CQRS sont exposes via Ecotone:

- `#[\Ecotone\Modelling\Attribute\CommandHandler]`
- `#[\Ecotone\Modelling\Attribute\QueryHandler]`
