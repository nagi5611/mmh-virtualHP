# airport/models/glb_to_usdz.py — GLB に白マテリアルを付与し USDZ に変換
"""Assign white PBR material to mesh-only GLB and convert to USDZ for iOS Quick Look."""

from __future__ import annotations

import argparse
import struct
import sys
from pathlib import Path

from pygltflib import GLTF2, Material, PbrMetallicRoughness
from pxr import Gf, Sdf, Usd, UsdGeom, UsdShade, UsdUtils, Vt

WHITE_RGBA = [1.0, 1.0, 1.0, 1.0]


def apply_white_material(gltf: GLTF2) -> None:
    """Assigns #ffffff PBR material to every mesh primitive."""
    gltf.materials = [
        Material(
            pbrMetallicRoughness=PbrMetallicRoughness(
                baseColorFactor=WHITE_RGBA,
                metallicFactor=0.0,
                roughnessFactor=0.75,
            ),
            doubleSided=True,
            name="White",
        )
    ]
    for mesh in gltf.meshes:
        for prim in mesh.primitives:
            prim.material = 0


def _accessor_data(gltf: GLTF2, accessor_index: int):
    accessor = gltf.accessors[accessor_index]
    buffer_view = gltf.bufferViews[accessor.bufferView]
    blob = gltf.binary_blob()
    if blob is None:
        raise RuntimeError("GLB has no binary blob")
    blob = blob[buffer_view.byteOffset : buffer_view.byteOffset + buffer_view.byteLength]
    dtype = {
        5120: "b",
        5121: "B",
        5122: "h",
        5123: "H",
        5125: "I",
        5126: "f",
    }[accessor.componentType]
    type_count = {
        "SCALAR": 1,
        "VEC2": 2,
        "VEC3": 3,
        "VEC4": 4,
    }[accessor.type]
    fmt = "<" + dtype * type_count
    stride = buffer_view.byteStride or struct.calcsize(fmt)
    start = accessor.byteOffset or 0
    values = []
    for i in range(accessor.count):
        offset = start + i * stride
        values.extend(struct.unpack_from(fmt, blob, offset))
    return values, accessor.type


def _build_mesh(gltf: GLTF2, mesh_index: int, stage: Usd.Stage, parent_path: str, name: str) -> None:
    mesh = gltf.meshes[mesh_index]
    mesh_path = f"{parent_path}/{name}"
    usd_mesh = UsdGeom.Mesh.Define(stage, mesh_path)

    points: list[Gf.Vec3f] = []
    face_vertex_counts: list[int] = []
    face_vertex_indices: list[int] = []

    for prim in mesh.primitives:
        if prim.mode not in (4, None):  # TRIANGLES
            continue
        pos_values, _ = _accessor_data(gltf, prim.attributes.POSITION)
        points = [Gf.Vec3f(pos_values[i], pos_values[i + 1], pos_values[i + 2]) for i in range(0, len(pos_values), 3)]

        if prim.indices is not None:
            idx_values, _ = _accessor_data(gltf, prim.indices)
            for i in range(0, len(idx_values), 3):
                face_vertex_counts.append(3)
                face_vertex_indices.extend(
                    [int(idx_values[i]), int(idx_values[i + 1]), int(idx_values[i + 2])]
                )
        else:
            for i in range(0, len(pos_values) // 3, 3):
                face_vertex_counts.append(3)
                face_vertex_indices.extend([i, i + 1, i + 2])

    if not points:
        return

    usd_mesh.CreatePointsAttr(Vt.Vec3fArray(points))
    usd_mesh.CreateFaceVertexCountsAttr(face_vertex_counts)
    usd_mesh.CreateFaceVertexIndicesAttr(face_vertex_indices)
    usd_mesh.CreateSubdivisionSchemeAttr("none")
    _bind_white_material(stage, usd_mesh)


def _bind_white_material(stage: Usd.Stage, usd_mesh: UsdGeom.Mesh) -> None:
    """Binds a white UsdPreviewSurface to the mesh."""
    mesh_path = usd_mesh.GetPath()
    material = UsdShade.Material.Define(stage, f"{mesh_path}/Material")
    shader = UsdShade.Shader.Define(stage, f"{mesh_path}/Material/Shader")
    shader.CreateIdAttr("UsdPreviewSurface")
    shader.CreateInput("diffuseColor", Sdf.ValueTypeNames.Color3f).Set(Gf.Vec3f(1.0, 1.0, 1.0))
    shader.CreateInput("roughness", Sdf.ValueTypeNames.Float).Set(0.75)
    shader.CreateInput("metallic", Sdf.ValueTypeNames.Float).Set(0.0)
    material.CreateSurfaceOutput().ConnectToSource(shader.ConnectableAPI(), "surface")
    UsdShade.MaterialBindingAPI(usd_mesh.GetPrim()).Bind(material)


def prepare_glb(glb_path: Path) -> None:
    """Writes white material into the GLB in place."""
    gltf = GLTF2().load(str(glb_path))
    apply_white_material(gltf)
    gltf.save(str(glb_path))


def convert(glb_path: Path, usdz_path: Path) -> None:
    gltf = GLTF2().load(str(glb_path))
    apply_white_material(gltf)
    usda_path = usdz_path.with_suffix(".usda")
    stage = Usd.Stage.CreateNew(str(usda_path))
    UsdGeom.SetStageUpAxis(stage, UsdGeom.Tokens.y)
    UsdGeom.SetStageMetersPerUnit(stage, 1.0)

    root = UsdGeom.Xform.Define(stage, "/Model")
    stage.SetDefaultPrim(root.GetPrim())

    for node_index, node in enumerate(gltf.nodes):
        if node.mesh is None:
            continue
        _build_mesh(gltf, node.mesh, stage, "/Model", f"mesh_{node_index}")

    stage.GetRootLayer().Save()
    ok = UsdUtils.CreateNewUsdzPackage(str(usda_path), str(usdz_path))
    if not ok:
        raise RuntimeError(f"Failed to create USDZ: {usdz_path}")
    usda_path.unlink(missing_ok=True)


def main() -> int:
    parser = argparse.ArgumentParser(description="Assign white material and convert GLB to USDZ")
    parser.add_argument("input_glb", type=Path)
    parser.add_argument("output_usdz", type=Path, nargs="?", default=None)
    parser.add_argument(
        "--glb-only",
        action="store_true",
        help="Only update the GLB with white material (skip USDZ)",
    )
    args = parser.parse_args()
    if args.glb_only:
        prepare_glb(args.input_glb)
        print(f"Updated {args.input_glb}")
        return 0
    output = args.output_usdz or args.input_glb.with_suffix(".usdz")
    prepare_glb(args.input_glb)
    convert(args.input_glb, output)
    print(f"Wrote {args.input_glb} and {output}")
    return 0


if __name__ == "__main__":
    sys.exit(main())
