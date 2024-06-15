{
    description = "PHP-Manager Flake";

    inputs = {
        nixPackages.url = "github:nixos/nixpkgs?ref=23.11";
        phps.url = "github:loophp/nix-shell";
        systems.url = "github:nix-systems/default";
    };

    outputs = inputs @ { self, flake-parts, ... }:
        # https://flake.parts/getting-started
        flake-parts.lib.mkFlake
            { inherit inputs; }
            {
                # Declare the systems supported by the flake.
                systems = import inputs.systems;

                perSystem = {
                    system,
                    ...
                }:
                    let
                        pkgs = import inputs.nixPackages {
                            inherit system;
                            overlays = [
                                inputs.phps.overlays.default
                            ];
                        };

                        devPackages = with pkgs; [
                            bash
                            php83
                            git
                            gnumake
                        ];
                    in {
                        devShells = {
                            default = pkgs.mkShell {
                                buildInputs = devPackages;
                            };
                        };
                    };
            };
}
