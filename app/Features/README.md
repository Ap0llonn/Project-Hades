## Vertical Slice + CQRS

Chaque use case vit dans son propre dossier (vertical slice), par exemple:

- `Passwords/CreatePassword`
- `Passwords/GetPasswordById`

Dans chaque slice:

- `*Command` ou `*Query`
- `*Handler`
- `*Result` / `*View`

Les handlers sont reliés au bus via `config/cqrs.php`.

