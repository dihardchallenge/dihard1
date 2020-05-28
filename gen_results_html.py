#!/usr/bin/env python
import argparse
import os
from pathlib import Path
import sys

from mako.template import Template
import pandas as pd
from lxml import etree


THIS_DIR = Path(__file__).parent.resolve()
TEMPLATE_PATH = Path(THIS_DIR, 'results_template.txt')


def load_final_leaderboards(submissions_path):
    """Extract final leaderboards from TSV containing all scored submissions.

    The input TSV file is expected to have the following columns

    - team_name  --  team name for display on leaderboards
    - system_name  --  system name for display on leaderboards
    - track  --  track submitted to; one of {Track1, Track2}
    - date  --  submission date (YYYY-MM-DD HH:MM:SS)
    - der  --  diarization error rate (%)
    - mi  --  mutual information (bits)
    - doi  --  Zenodo DOI for submission
    - description_basename  --  filename of system description
    - url  --  URL of Zenodo deposit

    Parameters
    ----------
    submissions_tsvf : Path
        Path to submissions TSV file.

    Returns
    -------
    pandas.DataFrame

    """
    def _filter_nonfinal(df):
        df = df.sort_values('date', ascending=True)
        df = df.groupby('team_system_name').last().reset_index()
        df = df.sort_values(['der', 'team_system_name'], ascending=True)
        return df
    submissions_path = Path(submissions_path)
    df = pd.read_csv(
        submissions_path, sep='\t', header=0, parse_dates=['date'])
    df['team_system_name'] = df.apply(lambda x: f'{x.team_name} ({x.system_name})', axis=1)
    df.description_basename.fillna('', inplace=True)
    
    # Split by track.
    track1_df = df[df.track == 'Track1']
    track2_df =	df[df.track == 'Track2']

    # Restrict to final submissions.
    track1_df = _filter_nonfinal(track1_df)
    track2_df = _filter_nonfinal(track2_df)
    
    return track1_df, track2_df


def main():
    parser = argparse.ArgumentParser(
        description='convert submission results to table in HTML form', add_help=True)
    parser.add_argument(
        'submissions', type=Path, help='path to submissions TSV')
    parser.add_argument(
        'results', type=Path, help='path to output results HTML')
    if len(sys.argv) == 1:
        parser.print_help()
        sys.exit(1)
    args = parser.parse_args()
    track1_df, track2_df = load_final_leaderboards(
        args.submissions)
    template = Template(filename=str(TEMPLATE_PATH))
    html = template.render(
        track1_rows=track1_df.itertuples(index=False),
        track2_rows=track2_df.itertuples(index=False))
    with open(args.results, 'w') as f:
        f.write(html)

    
if __name__ == '__main__':
    main()

# Team_Name	System_Name	Track	Date	DER	MI	DOI	SystemDescription	URL
# BISC	SYSTEM_1	Track1	"2018-03-07 17:07:12"	48.31	8.5	10.5281/zenodo.1193762	bisc_systems.pdf	https://zenodo.org/record/1193762
# BISC	SYSTEM_1	Track1	"2018-03-07 22:01:21"	47.44	8.47	10.5281/zenodo.1193919	bisc_systems.pdf	https://zenodo.org/record/1193919
