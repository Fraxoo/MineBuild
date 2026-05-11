# MCD — Plateforme Builds Minecraft 

```mermaid
erDiagram
  USER {
    UUID id PK
    VARCHAR email UK
    VARCHAR username UK
    VARCHAR password
    TEXT avatar_url
    TEXT bio
    UUID role_id FK
    BOOLEAN is_active
    DATETIME created_at
    DATETIME updated_at
  }

  ROLE {
    UUID id PK
    VARCHAR code UK
    DATETIME created_at
    DATETIME updated_at
  }

  BUILD {
    UUID id PK
    UUID author_id FK
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
    UUID hidden_by FK
    DATETIME hidden_at
    INT views_count
    INT likes_count
    INT saves_count
    INT downloads_count
    INT ratings_count
    DECIMAL rating_avg
    DATETIME created_at
    DATETIME updated_at
    DATETIME deleted_at
  }

  CATEGORY {
    UUID id PK
    VARCHAR name UK
    VARCHAR slug UK
    DATETIME created_at
    DATETIME updated_at
  }

  BUILD_CATEGORY {
    UUID build_id PK,FK
    UUID category_id PK,FK
    DATETIME created_at
  }

  BUILD_IMAGE {
    UUID id PK
    UUID build_id FK
    TEXT url
    VARCHAR alt
    INT sort_order
    DATETIME created_at
  }

  TAG {
    UUID id PK
    VARCHAR name UK
    VARCHAR slug UK
    DATETIME created_at
    DATETIME updated_at
  }

  BUILD_TAG {
    UUID build_id PK,FK
    UUID tag_id PK,FK
    DATETIME created_at
  }

  BUILD_MATERIAL {
    UUID id PK
    UUID build_id FK
    VARCHAR name
    INT quantity
    VARCHAR color
    DATETIME created_at
    DATETIME updated_at
  }

  COMMENT {
    UUID id PK
    UUID build_id FK
    UUID author_id FK
    TEXT content
    VARCHAR visibility
    TEXT hidden_reason
    UUID hidden_by FK
    DATETIME hidden_at
    DATETIME created_at
    DATETIME updated_at
    DATETIME deleted_at
  }

  BUILD_LIKE {
    UUID build_id PK,FK
    UUID user_id PK,FK
    DATETIME created_at
  }

  BUILD_SAVE {
    UUID build_id PK,FK
    UUID user_id PK,FK
    DATETIME created_at
  }

  BUILD_RATING {
    UUID build_id PK,FK
    UUID user_id PK,FK
    INT rating
    DATETIME created_at
    DATETIME updated_at
  }

  USER_FOLLOW {
    UUID follower_id PK,FK
    UUID following_id PK,FK
    DATETIME created_at
  }

  NOTIFICATION {
    UUID id PK
    UUID recipient_id FK
    UUID actor_id FK
    VARCHAR type
    UUID build_id FK
    UUID comment_id FK
    TEXT message
    DATETIME read_at
    DATETIME created_at
  }

  REPORT {
    UUID id PK
    UUID reporter_id FK
    VARCHAR target_type
    UUID build_id FK
    UUID comment_id FK
    VARCHAR reason_code
    TEXT message
    VARCHAR status
    UUID handled_by FK
    DATETIME handled_at
    DATETIME created_at
    DATETIME updated_at
  }

  MODERATION_ACTION {
    UUID id PK
    UUID moderator_id FK
    VARCHAR target_type
    UUID build_id FK
    UUID comment_id FK
    VARCHAR action
    TEXT reason
    DATETIME created_at
  }

  BUILD_ASSET {
    UUID id PK
    UUID build_id FK
    VARCHAR type
    TEXT url
    VARCHAR filename
    INT size_bytes
    INT downloads_count
    DATETIME created_at
  }

  %% RELATIONS
  ROLE ||--o{ USER : has
  USER ||--o{ BUILD : creates
  USER ||--o{ COMMENT : writes

  BUILD ||--o{ BUILD_IMAGE : has
  BUILD ||--o{ BUILD_MATERIAL : requires
  BUILD ||--o{ COMMENT : has

  BUILD ||--o{ BUILD_LIKE : liked_by
  USER ||--o{ BUILD_LIKE : likes

  BUILD ||--o{ BUILD_SAVE : saved_by
  USER ||--o{ BUILD_SAVE : saves

  BUILD ||--o{ BUILD_RATING : rated_by
  USER ||--o{ BUILD_RATING : rates

  USER ||--o{ USER_FOLLOW : follower
  USER ||--o{ USER_FOLLOW : following

  TAG ||--o{ BUILD_TAG : used_in
  BUILD ||--o{ BUILD_TAG : tagged_by

  CATEGORY ||--o{ BUILD_CATEGORY : used_in
  BUILD ||--o{ BUILD_CATEGORY : has

  USER ||--o{ NOTIFICATION : recipient
  USER ||--o{ NOTIFICATION : actor
  BUILD ||--o{ NOTIFICATION : related_build
  COMMENT ||--o{ NOTIFICATION : related_comment

  USER ||--o{ REPORT : reports
  BUILD ||--o{ REPORT : reported_build
  COMMENT ||--o{ REPORT : reported_comment

  USER ||--o{ MODERATION_ACTION : moderates
  BUILD ||--o{ MODERATION_ACTION : moderated_build
  COMMENT ||--o{ MODERATION_ACTION : moderated_comment

  BUILD ||--o{ BUILD_ASSET : contains