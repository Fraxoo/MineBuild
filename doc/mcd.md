# MCD - Plateforme de partage de builds Minecraft

Ce MCD reflète les entités Doctrine présentes dans `src/Entity`.

```mermaid
erDiagram
  USER {
    INT id PK
    VARCHAR username UK
    VARCHAR password
    VARCHAR email UK
    TEXT avatar_url
    TEXT bio
    INT role_id FK
    BOOLEAN is_active
    DATETIME created_at
    DATETIME updated_at
  }

  ROLE {
    INT id PK
    VARCHAR code UK
    DATETIME created_at
    DATETIME updated_at
  }

  BUILD {
    INT id PK
    INT author_id FK
    VARCHAR title
    TEXT description
    INT dimensions_x
    INT dimensions_y
    INT dimensions_z
    VARCHAR difficulty
    INT time_estimated_min
    VARCHAR game_version
    VARCHAR game_mode
    VARCHAR visibility
    TEXT hidden_reason
    INT hidden_by_id FK
    DATETIME hidden_at
    INT views_count
    INT likes_count
    INT saves_count
    INT downloads_count
    INT ratings_count
    DECIMAL rating_avg
    BOOLEAN modded
    DATETIME created_at
    DATETIME updated_at
    DATETIME deleted_at
  }

  CATEGORY {
    INT id PK
    VARCHAR name UK
    VARCHAR name_fr
    DATETIME created_at
    DATETIME updated_at
  }

  BUILD_CATEGORY {
    INT build_id PK,FK
    INT category_id PK,FK
    DATETIME created_at
  }

  BUILD_IMAGE {
    INT id PK
    INT build_id FK
    TEXT url
    VARCHAR alt
    INT sort_order
    DATETIME created_at
  }

  TAG {
    INT id PK
    VARCHAR name UK
    VARCHAR slug UK
    DATETIME created_at
    DATETIME updated_at
  }

  BUILD_TAG {
    INT build_id PK,FK
    INT tag_id PK,FK
    DATETIME created_at
  }

  BUILD_MATERIAL {
    INT id PK
    INT build_id FK
    VARCHAR name
    INT quantity
    VARCHAR color
    DATETIME created_at
    DATETIME updated_at
  }

  COMMENT {
    INT id PK
    INT build_id FK
    INT author_id FK
    TEXT content
    DATETIME created_at
    DATETIME updated_at
    DATETIME deleted_at
  }

  COMMENT_LIKE {
    INT id PK
    INT user_id FK
    INT comment_id FK
  }

  BUILD_LIKE {
    INT build_id PK,FK
    INT user_id PK,FK
    DATETIME created_at
  }

  BUILD_SAVE {
    INT build_id PK,FK
    INT user_id PK,FK
    DATETIME created_at
  }

  BUILD_RATING {
    INT build_id PK,FK
    INT user_id PK,FK
    INT rating
    DATETIME created_at
    DATETIME updated_at
  }

  BUILD_DOWNLOAD {
    INT id PK
    INT build_id FK
    INT user_id FK
    DATETIME created_at
  }

  USER_FOLLOW {
    INT follower_id PK,FK
    INT following_id PK,FK
    DATETIME created_at
  }

  NOTIFICATION {
    INT id PK
    INT recipient_id FK
    INT actor_id FK
    VARCHAR type
    INT build_id FK
    INT comment_id FK
    TEXT message
    DATETIME read_at
    DATETIME created_at
  }

  REPORT {
    INT id PK
    INT reporter_id FK
    VARCHAR target_type
    INT build_id FK
    INT comment_id FK
    VARCHAR reason_code
    TEXT message
    VARCHAR status
    INT handled_by_id FK
    DATETIME handled_at
    DATETIME created_at
    DATETIME updated_at
  }

  MODERATION_ACTION {
    INT id PK
    INT moderator_id FK
    VARCHAR target_type
    INT build_id FK
    INT comment_id FK
    VARCHAR action
    TEXT reason
    DATETIME created_at
  }

  BUILD_ASSET {
    INT id PK
    INT build_id FK
    VARCHAR type
    TEXT url
    VARCHAR filename
    INT size_bytes
    INT downloads_count
    DATETIME created_at
  }

  ROLE ||--o{ USER : has
  USER ||--o{ BUILD : creates
  USER ||--o{ COMMENT : writes

  BUILD ||--o{ BUILD_IMAGE : has
  BUILD ||--o{ BUILD_MATERIAL : requires
  BUILD ||--o{ BUILD_ASSET : contains
  BUILD ||--o{ COMMENT : has

  BUILD ||--o{ BUILD_LIKE : liked_by
  USER ||--o{ BUILD_LIKE : likes

  BUILD ||--o{ BUILD_SAVE : saved_by
  USER ||--o{ BUILD_SAVE : saves

  BUILD ||--o{ BUILD_RATING : rated_by
  USER ||--o{ BUILD_RATING : rates

  BUILD ||--o{ BUILD_DOWNLOAD : downloaded_as
  USER ||--o{ BUILD_DOWNLOAD : downloads

  COMMENT ||--o{ COMMENT_LIKE : liked_by
  USER ||--o{ COMMENT_LIKE : likes_comment

  USER ||--o{ USER_FOLLOW : follower
  USER ||--o{ USER_FOLLOW : following

  TAG ||--o{ BUILD_TAG : used_in
  BUILD ||--o{ BUILD_TAG : tagged_by

  CATEGORY ||--o{ BUILD_CATEGORY : used_in
  BUILD ||--o{ BUILD_CATEGORY : categorized_by

  USER ||--o{ NOTIFICATION : receives
  USER ||--o{ NOTIFICATION : triggers
  BUILD ||--o{ NOTIFICATION : related_build
  COMMENT ||--o{ NOTIFICATION : related_comment

  USER ||--o{ REPORT : reports
  USER ||--o{ REPORT : handles
  BUILD ||--o{ REPORT : reported_build
  COMMENT ||--o{ REPORT : reported_comment

  USER ||--o{ MODERATION_ACTION : moderates
  BUILD ||--o{ MODERATION_ACTION : moderated_build
  COMMENT ||--o{ MODERATION_ACTION : moderated_comment
```

## Notes de cohérence avec les entités

- Les identifiants principaux simples sont des `int` auto-générés, pas des `UUID`.
- Les tables `BUILD_LIKE`, `BUILD_SAVE`, `BUILD_RATING`, `BUILD_TAG`, `BUILD_CATEGORY` et `USER_FOLLOW` utilisent une clé primaire composée basée sur leurs relations.
- `COMMENT_LIKE` et `BUILD_DOWNLOAD` possèdent actuellement un `id` technique auto-généré.
- `Build` contient maintenant le booléen `modded`, utile pour distinguer les builds vanilla et moddés.
- `Category` contient `name` et `name_fr`, avec unicité sur `name`.
