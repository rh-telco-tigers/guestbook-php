# Intro

The following presentation is created using [Marp](https://marp.app/#get-started). In order to generate a new version of the presentation run the following:

```
# Convert slide deck into HTML
podman run --rm -v $PWD:/home/marp/app/:z -e LANG=$LANG docker.io/marpteam/marp-cli:latest slide-deck.md
```

or if you have Marp installed run:

```
$ marp slide-deck.md
[  INFO ] Converting 1 markdown...
[  INFO ] slide-deck.md => slide-deck.html
```

## Viewing the presentation

Using your favorite Web Browser open the "slide-deck.html" file in this directory. You can use the right and left arrow keys on your keyboard to move through the presentation. You can also press the "p" key and it will open a new window with the slides as well as the presenter notes, to guide you through the presentation.