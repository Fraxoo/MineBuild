## Prerequisites

Before getting started, make sure you have:

1. Docker installed on your system
2. Ports `80` and `443` available

## Installation

Open a terminal in the project directory and build the Docker containers:

```bash
docker compose up --build
```

Once it is done, open [https://localhost](https://localhost) in your browser (or [http://localhost](http://localhost) without HTTPS).

The application is now ready to use!

## Notes

To close the project : 
```bash
docker compose down
```

This project is still under development, so you may encounter bugs or incomplete features.
