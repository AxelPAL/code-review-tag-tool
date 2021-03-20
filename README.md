# Code Review Tool

This instrument is aimed to count specific tags in Pull Request's comments at BitBucket.

## XDebug
For using XDebug you should add arg to your compose-file (to app service):
```yaml
    build:
      args:
        INSTALL_XDEBUG: "true"
```