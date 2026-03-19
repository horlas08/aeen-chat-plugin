# Plugins – Plans & Packaging

## Salla plugin
- Plan: `plugins/salla/plan.md`

## Packaging (ZIP)
Create a `dist/` folder at repo root (if it doesn’t exist) and build a ZIP from repo root:

```bash
mkdir -p dist
zip -r dist/salla-plugin.zip plugins/salla -x "**/.DS_Store" "**/node_modules/**" "**/tmp/**"
```

Notes:
- The ZIP should include only `plugins/salla/**`.
- Don’t commit `dist/` artifacts to git.
