# SketchApp Reader for Flow Framework

## Description

Currently work only on OSX, use the `sketchtool` CLI binary.

Lots of code stollen fro [pixotech/showcase](https://github.com/pixotech/showcase).

## Usage

    ./flow sketchapp:importartboards --file /Users/dfeyer/Google\ Drive/Neos/Design/Neos\ UI\ resources/UI\ Kit\ \(Work\ in\ progress\)/Neos\ UI\ Kit.sketch --name UiKit
    
After the command, you can check `./Data/Persistent/SketchApp/Exports/UiKit` and you should see some PNG files.

In the Media module you have a new AssetCollection (use the `name` argument), and all your Artboards availables as high definitions PNG images.

As this package run only on Mac OSX, we should add a API to update those Assets remotly.

## Sponsors & Contributors

The development of this package is sponsored by ttree (https://ttree.ch).
