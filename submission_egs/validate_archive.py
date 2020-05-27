#!/usr/bin/env python
"""Validate submission archive for DIHARD.

Expected format of archive is:

- some_dir/
- some_dir/track1/
- some_dir/track2/

where ``track1`` and ``track2`` are directories of RTTM files. At least one of
``track1`` and ``track2`` must be present.

All errors will be printed to STDOUT.

Example usage:

    python validate_archive.py submission.tar.gz
"""
from __future__ import print_function
from __future__ import unicode_literals
import argparse
from collections import defaultdict
import os
import sys
import tarfile


def error(msg):
    """Print error message ``msg`` to STDOUT."""
    print('ERROR: %s' % msg)


VALID_TRACK_NAMES = set(['track1', 'track2'])    
REF_RTTM_BNS = set(['DH_%.4d.rttm' % n for n in range(1, 173)])

def check_archive(tarf):
    """Validate directory structure of (possibly compressed) tarball ``tarf``.
    """
    with tarfile.open(tarf, 'r') as f:
        # Check for unexpected files and directories.
        root_dirs = []
        track_to_rttms = defaultdict(list)
        track_dirs = []
        for member in f.getmembers():
            name = member.name
            depth = name.count('/')
            bn = name.split('/')[-1]
            is_dir = member.isdir()
            is_track_dir = is_dir and depth == 1 and bn in VALID_TRACK_NAMES
            is_rttm = not is_dir and depth == 2 and bn.endswith('.rttm')
            if is_dir:
                print(name)
                if depth == 0:
                    root_dirs.append(name)
                elif is_track_dir:
                    track_dirs.append(name)
                else:
                    error('Unexpected directory found: %s' % name)
            else:
                if is_rttm:
                    track_name = name.split('/')[-2]
                    if track_name in VALID_TRACK_NAMES:
                        track_to_rttms[track_name].append(bn)
                    else:
                        error('Unexpected file found: %s' % name)
                else:
                    error('Unexpected file found: %s'  % name)

        # Check for presence of root directory.
        if not root_dirs:
            error('No top level directory found.')
    
        # Check for at least one track directory.
        if not track_to_rttms:
            error('No track directories found.')
            

        # Check that all and only the expected RTTMs are present for each
        # track.
        for track, rttm_bns in track_to_rttms.items():
            rttm_bns = set(rttm_bns)
            miss_bns = REF_RTTM_BNS - rttm_bns
            fa_bns = rttm_bns - REF_RTTM_BNS
            if miss_bns:
                error('Missing RTTMs for track "%s": %s' %
                      (track, ', '.join(sorted(miss_bns))))
            if fa_bns:
                error('Unexpected RTTMs present for track "%s": %s' %
                      (track, ', '.join(sorted(fa_bns))))


if __name__ == '__main__':
    parser = argparse.ArgumentParser(
        description='Validate directory structure of archive.',
        add_help=True,
        usage='%(prog)s tarf')
    parser.add_argument(
        'tarf',
        help='tarball')
    if len(sys.argv) == 1:
        parser.print_help()
        sys.exit(1)
    args = parser.parse_args()
    check_archive(args.tarf)
