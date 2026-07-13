# MCD — Plateforme de partage de builds Minecraft

```mermaid
erDiagram
  USER {
    string username
    string email
    string password
    string avatar_url
    string bio
    boolean is_active
    number reports_count
  }

  ROLE {
    string code
  }

  BUILD {
    string title
    string description
    number dimensions_x
    number dimensions_y
    number dimensions_z
    enum difficulty "easy|medium|hard|expert"
    number time_estimated_min
    enum game_mode "survival|creative"
    enum visibility "PUBLIC|HIDDEN"
    number views_count
    number likes_count
    number saves_count
    number downloads_count
    number ratings_count
    number rating_avg
    boolean modded
    string deleted_reason
  }

  CATEGORY {
    string name
    string name_fr
  }

  TAG {
    string name
    string slug
  }

  MCVERSION {
    string number
  }

  BUILD_IMAGE {
    string url
    string alt
    number sort_order
  }

  BUILD_MATERIAL {
    string name
    number quantity
    string color
  }

  BUILD_ASSET {
    enum type "world"
    string url
    string filename
    number size_bytes
    number downloads_count
  }

  COMMENT {
    string content
    enum visibility "PUBLIC|HIDDEN"
  }

  NOTIFICATION {
    enum type "follow|like|comment|rating|moderation"
    string message
    datetime read_at
  }

  REPORT {
    enum target_type "build|comment|user"
    enum reason_code "spam|harassment|hate_speech|nudity|violence|illegal|impersonation|copyright|other"
    string message
    enum status "Pending|Confirmed|Rejected"
    datetime handled_at
  }

  MODERATION_ACTION {
    enum target_type "build|comment|user"
    enum action "Delete"
    string reason
    enum reason_code "spam|harassment|hate_speech|nudity|violence|illegal|impersonation|copyright|other"
  }

  BUILD_CATEGORY {
    datetime created_at
  }

  BUILD_TAG {
    datetime created_at
  }

  BUILD_LIKE {
    datetime created_at
  }

  BUILD_SAVE {
    datetime created_at
  }

  BUILD_RATING {
    number rating
    datetime created_at
    datetime updated_at
  }

  BUILD_DOWNLOAD {
    datetime created_at
  }

  BUILD_VIEW {
    string ip_hash
    datetime viewed_at
  }

  FOLLOW {
    datetime created_at
  }

  ROLE ||--o{ USER : attribue

  USER ||--o{ BUILD : cree
  USER o|--o{ BUILD : supprime

  BUILD ||--o{ BUILD_IMAGE : illustre
  BUILD ||--o{ BUILD_MATERIAL : necessite
  BUILD ||--o{ BUILD_ASSET : propose

  BUILD ||--o{ BUILD_CATEGORY : categorise
  CATEGORY ||--o{ BUILD_CATEGORY : contient

  BUILD ||--o{ BUILD_TAG : tague
  TAG ||--o{ BUILD_TAG : utilise

  BUILD }o--o{ MCVERSION : prend_en_charge

  USER ||--o{ COMMENT : redige
  BUILD ||--o{ COMMENT : contient

  USER }o--o{ COMMENT : aime

  USER ||--o{ BUILD_LIKE : aime
  BUILD ||--o{ BUILD_LIKE : est_aime

  USER ||--o{ BUILD_SAVE : sauvegarde
  BUILD ||--o{ BUILD_SAVE : est_sauvegarde

  USER ||--o{ BUILD_RATING : note
  BUILD ||--o{ BUILD_RATING : est_note

  USER ||--o{ BUILD_DOWNLOAD : telecharge
  BUILD ||--o{ BUILD_DOWNLOAD : est_telecharge

  BUILD ||--o{ BUILD_VIEW : comptabilise
  USER o|--o{ BUILD_VIEW : consulte

  USER ||--o{ FOLLOW : suit
  USER ||--o{ FOLLOW : est_suivi

  USER ||--o{ NOTIFICATION : recoit
  USER o|--o{ NOTIFICATION : declenche
  BUILD o|--o{ NOTIFICATION : concerne
  COMMENT o|--o{ NOTIFICATION : concerne

  USER ||--o{ REPORT : effectue
  USER o|--o{ REPORT : traite
  USER o|--o{ REPORT : est_signale
  BUILD o|--o{ REPORT : est_signale
  COMMENT o|--o{ REPORT : est_signale

  USER ||--o{ MODERATION_ACTION : realise
  USER o|--o{ MODERATION_ACTION : subit
  BUILD o|--o{ MODERATION_ACTION : concerne
  COMMENT o|--o{ MODERATION_ACTION : concerne
  REPORT o|--o| MODERATION_ACTION : produit
```
